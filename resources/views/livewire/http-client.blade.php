<?php

use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $method = 'GET';
    public string $url = '';
    public string $body = '';
    public array $headers = [['key' => '', 'value' => '']];
    public array $queryParams = [['key' => '', 'value' => '']];
    
    public ?array $response = null;
    public bool $loading = false;
    public array $history = [];

    public function mount(): void
    {
        $this->history = session('request_history', []);
    }

    public function addHeader(): void
    {
        $this->headers[] = ['key' => '', 'value' => ''];
    }

    public function removeHeader(int $index): void
    {
        unset($this->headers[$index]);
        $this->headers = array_values($this->headers);
    }

    public function addQueryParam(): void
    {
        $this->queryParams[] = ['key' => '', 'value' => ''];
    }

    public function removeQueryParam(int $index): void
    {
        unset($this->queryParams[$index]);
        $this->queryParams = array_values($this->queryParams);
    }

    public function sendRequest(): void
    {
        $this->loading = true;
        $this->response = null;

        try {
            $preparedHeaders = collect($this->headers)
                ->filter(fn($h) => !empty($h['key']))
                ->pluck('value', 'key')
                ->toArray();

            $preparedQueryParams = collect($this->queryParams)
                ->filter(fn($p) => !empty($p['key']))
                ->pluck('value', 'key')
                ->toArray();

            $startTime = microtime(true);
            
            $request = Http::withHeaders($preparedHeaders);
            
            if (!empty($preparedQueryParams)) {
                $request->withQueryParameters($preparedQueryParams);
            }

            $method = strtoupper($this->method);
            
            if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
                $res = $request->withBody($this->body, 'application/json')->send($method, $this->url);
            } else {
                $res = $request->send($method, $this->url);
            }

            $duration = round((microtime(true) - $startTime) * 1000);

            $this->response = [
                'status' => $res->status(),
                'headers' => $res->headers(),
                'content' => $res->body(),
                'duration' => $duration,
                'size' => strlen($res->body()),
            ];

            // Add to history
            $historyItem = [
                'id' => Str::uuid()->toString(),
                'method' => $this->method,
                'url' => $this->url,
                'status' => $res->status(),
                'time' => now()->format('H:i:s'),
            ];
            
            array_unshift($this->history, $historyItem);
            $this->history = array_slice($this->history, 0, 20); // Keep last 20
            session(['request_history' => $this->history]);

        } catch (\Exception $e) {
            $this->response = [
                'error' => $e->getMessage(),
            ];
        }

        $this->loading = false;
    }

    public function loadFromHistory(string $id): void
    {
        // For a real app we'd store full request data in history,
        // but for this MVP let's just find the item and set url/method.
        $item = collect($this->history)->firstWhere('id', $id);
        if ($item) {
            $this->method = $item['method'];
            $this->url = $item['url'];
        }
    }

    public function clearHistory(): void
    {
        $this->history = [];
        session()->forget('request_history');
    }
};
?>

<div class="flex h-[calc(100vh-56px)] overflow-hidden bg-gray-100">
    <!-- Sidebar: History -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 uppercase text-xs tracking-wider">History</h3>
            <button wire:click="clearHistory" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto">
            @if(empty($history))
                <div class="p-4 text-center text-gray-400 text-sm italic">
                    No history yet
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($history as $item)
                        <button wire:click="loadFromHistory('{{ $item['id'] }}')" class="w-full text-left p-3 hover:bg-gray-50 transition duration-150 group">
                            <div class="flex items-center space-x-2">
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ in_array($item['method'], ['POST', 'PUT']) ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $item['method'] }}
                                </span>
                                <span class="text-xs font-medium text-gray-500">{{ $item['time'] }}</span>
                                <span class="text-xs font-bold {{ $item['status'] < 400 ? 'text-green-500' : 'text-red-500' }}">{{ $item['status'] }}</span>
                            </div>
                            <div class="mt-1 text-xs text-gray-700 truncate font-mono">
                                {{ $item['url'] }}
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Request Bar -->
        <div class="bg-white p-4 border-b border-gray-200">
            <div class="flex space-x-2">
                <div class="flex-1 flex border border-gray-300 rounded-md overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                    <select wire:model="method" class="bg-gray-50 border-none text-gray-700 font-bold text-sm px-4 focus:ring-0">
                        <option>GET</option>
                        <option>POST</option>
                        <option>PUT</option>
                        <option>PATCH</option>
                        <option>DELETE</option>
                        <option>OPTIONS</option>
                        <option>HEAD</option>
                    </select>
                    <input wire:model="url" type="text" placeholder="https://api.example.com/v1/resource" class="flex-1 border-none text-sm focus:ring-0">
                </div>
                <button wire:click="sendRequest" wire:loading.attr="disabled" class="bg-blue-600 text-white px-8 py-2 rounded-md font-bold hover:bg-blue-700 transition shadow-sm flex items-center">
                    <span wire:loading.remove>SEND</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        SENDING...
                    </span>
                </button>
            </div>
        </div>

        <!-- Request Tabs & Editor -->
        <div class="flex-1 flex flex-col min-h-0 bg-white" x-data="{ activeTab: 'params' }">
            <div class="px-4 border-b border-gray-200 bg-gray-50/50">
                <nav class="-mb-px flex space-x-6">
                    <button @click="activeTab = 'params'" :class="activeTab === 'params' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3 px-1 border-b-2 font-bold text-[11px] uppercase tracking-widest transition">Params</button>
                    <button @click="activeTab = 'headers'" :class="activeTab === 'headers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3 px-1 border-b-2 font-bold text-[11px] uppercase tracking-widest transition">Headers</button>
                    <button @click="activeTab = 'body'" :class="activeTab === 'body' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-3 px-1 border-b-2 font-bold text-[11px] uppercase tracking-widest transition">Body</button>
                </nav>
            </div>

            <div class="flex-1 overflow-y-auto p-4 font-mono text-sm">
                <!-- Params Editor -->
                <div x-show="activeTab === 'params'" class="space-y-2">
                    <h4 class="text-[10px] text-gray-400 font-bold uppercase mb-3">Query Parameters</h4>
                    @foreach($queryParams as $index => $param)
                        <div class="flex space-x-1 group">
                            <input wire:model="queryParams.{{ $index }}.key" type="text" placeholder="Parameter" class="flex-1 border-gray-200 rounded text-xs p-1.5 focus:border-blue-400 focus:ring-0">
                            <input wire:model="queryParams.{{ $index }}.value" type="text" placeholder="Value" class="flex-1 border-gray-200 rounded text-xs p-1.5 focus:border-blue-400 focus:ring-0">
                            <button wire:click="removeQueryParam({{ $index }})" class="px-2 text-gray-300 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endforeach
                    <button wire:click="addQueryParam" class="text-[10px] font-bold text-blue-600 hover:text-blue-700">+ ADD PARAMETER</button>
                </div>

                <!-- Headers Editor -->
                <div x-show="activeTab === 'headers'" class="space-y-2">
                    <h4 class="text-[10px] text-gray-400 font-bold uppercase mb-3">Request Headers</h4>
                    @foreach($headers as $index => $header)
                        <div class="flex space-x-1 group">
                            <input wire:model="headers.{{ $index }}.key" type="text" placeholder="Header" class="flex-1 border-gray-200 rounded text-xs p-1.5 focus:border-blue-400 focus:ring-0">
                            <input wire:model="headers.{{ $index }}.value" type="text" placeholder="Value" class="flex-1 border-gray-200 rounded text-xs p-1.5 focus:border-blue-400 focus:ring-0">
                            <button wire:click="removeHeader({{ $index }})" class="px-2 text-gray-300 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endforeach
                    <button wire:click="addHeader" class="text-[10px] font-bold text-blue-600 hover:text-blue-700">+ ADD HEADER</button>
                </div>

                <!-- Body Editor -->
                <div x-show="activeTab === 'body'" class="h-full flex flex-col">
                    <div class="flex items-center space-x-4 mb-4 text-[10px] font-bold text-gray-500">
                        <label class="flex items-center space-x-1">
                            <input type="radio" name="body_type" checked class="text-blue-600 focus:ring-0">
                            <span>JSON</span>
                        </label>
                    </div>
                    <textarea wire:model="body" class="flex-1 w-full border-gray-200 rounded p-4 text-xs font-mono focus:border-blue-400 focus:ring-0 resize-none min-h-[150px]" placeholder='{ "key": "value" }'></textarea>
                </div>
            </div>

            <!-- Response Pane (Integrated) -->
            <div class="h-1/2 border-t border-gray-200 flex flex-col bg-white">
                <div class="px-4 py-2 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-[10px] uppercase tracking-widest text-gray-500">Response</h3>
                    @if($response && !isset($response['error']))
                        <div class="flex space-x-4 text-[10px] font-bold">
                            <span class="{{ $response['status'] < 400 ? 'text-green-600' : 'text-red-600' }}">STATUS: {{ $response['status'] }}</span>
                            <span class="text-gray-400">TIME: {{ $response['duration'] }}ms</span>
                            <span class="text-gray-400">SIZE: {{ number_format($response['size'] / 1024, 2) }}KB</span>
                        </div>
                    @endif
                </div>
                
                <div class="flex-1 overflow-hidden flex flex-col" x-data="{ resTab: 'body' }">
                    @if($response)
                        @if(isset($response['error']))
                            <div class="p-8 text-center">
                                <div class="inline-block p-4 bg-red-50 rounded-lg text-red-600 text-sm font-medium">
                                    {{ $response['error'] }}
                                </div>
                            </div>
                        @else
                            <div class="px-4 border-b border-gray-200">
                                <nav class="-mb-px flex space-x-6">
                                    <button @click="resTab = 'body'" :class="resTab === 'body' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-2 px-1 border-b-2 font-bold text-[10px] uppercase transition">Body</button>
                                    <button @click="resTab = 'headers'" :class="resTab === 'headers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-2 px-1 border-b-2 font-bold text-[10px] uppercase transition">Headers</button>
                                </nav>
                            </div>
                            <div class="flex-1 overflow-y-auto p-0 font-mono text-sm bg-gray-900">
                                <div x-show="resTab === 'body'" class="h-full">
                                    <pre class="p-4 text-green-400 whitespace-pre-wrap"><code>{{ $response['content'] }}</code></pre>
                                </div>
                                <div x-show="resTab === 'headers'" class="p-4 space-y-1">
                                    @foreach($response['headers'] as $name => $values)
                                        <div class="text-[11px]">
                                            <span class="text-blue-400 font-bold">{{ is_array($name) ? implode(', ', $name) : $name }}:</span>
                                            <span class="text-gray-300 ml-2">{{ is_array($values) ? implode(', ', $values) : $values }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center text-gray-300">
                            <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <p class="text-sm font-medium italic">Enter URL and click SEND to see response</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
