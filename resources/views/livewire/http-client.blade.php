<?php

use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Environment;
use App\Models\RequestCollection;
use App\Models\HttpClientRequest;
use Illuminate\Support\Str;

new #[Layout('layouts.app')] class extends Component
{
    // Request Data
    public string $method = 'GET';
    public string $url = '';
    public string $body = '';
    public array $headers = [['key' => '', 'value' => '']];
    public array $queryParams = [['key' => '', 'value' => '']];
    public string $body_type = 'json';

    // State
    public ?array $response = null;
    public bool $loading = false;
    public array $history = [];
    public $environments = [];
    public $collections = [];
    public ?string $selectedEnvironmentId = null;
    public ?string $selectedCollectionId = null;
    public string $requestName = 'Untitled Request';

    // Modals/UI State
    public bool $showEnvModal = false;
    public bool $showSaveModal = false;
    public array $editingEnv = ['name' => '', 'variables' => [['key' => '', 'value' => '']]];

    public function mount(): void
    {
        $this->history = session('request_history', []);
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->environments = Environment::all();
        $this->collections = RequestCollection::with('requests')->get();
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

    protected function substituteVariables(string $content): string
    {
        if (!$this->selectedEnvironmentId) return $content;

        $env = Environment::find($this->selectedEnvironmentId);
        if (!$env || empty($env->variables)) return $content;

        foreach ($env->variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }

    public function sendRequest(): void
    {
        $this->loading = true;
        $this->response = null;

        try {
            $substitutedUrl = $this->substituteVariables($this->url);
            
            $preparedHeaders = collect($this->headers)
                ->filter(fn($h) => !empty($h['key']))
                ->mapWithKeys(fn($h) => [$h['key'] => $this->substituteVariables($h['value'])])
                ->toArray();

            $preparedQueryParams = collect($this->queryParams)
                ->filter(fn($p) => !empty($p['key']))
                ->mapWithKeys(fn($p) => [$p['key'] => $this->substituteVariables($p['value'])])
                ->toArray();

            $substitutedBody = $this->substituteVariables($this->body);

            $startTime = microtime(true);
            
            $request = Http::withHeaders($preparedHeaders);
            
            if (!empty($preparedQueryParams)) {
                $request->withQueryParameters($preparedQueryParams);
            }

            $method = strtoupper($this->method);
            
            if (in_array($method, ['POST', 'PUT', 'PATCH']) && !empty($substitutedBody)) {
                $res = $request->withBody($substitutedBody, 'application/json')->send($method, $substitutedUrl);
            } else {
                $res = $request->send($method, $substitutedUrl);
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
            $this->history = array_slice($this->history, 0, 20);
            session(['request_history' => $this->history]);

        } catch (\Exception $e) {
            $this->response = [
                'error' => $e->getMessage(),
            ];
        }

        $this->loading = false;
    }

    public function openSaveModal(): void
    {
        $this->showSaveModal = true;
    }

    public function saveRequest(): void
    {
        $this->validate([
            'requestName' => 'required|string|max:255',
            'selectedCollectionId' => 'required|uuid',
        ]);

        HttpClientRequest::create([
            'request_collection_id' => $this->selectedCollectionId,
            'name' => $this->requestName,
            'method' => $this->method,
            'url' => $this->url,
            'headers' => $this->headers,
            'query_params' => $this->queryParams,
            'body' => $this->body,
            'body_type' => $this->body_type,
        ]);

        $this->showSaveModal = false;
        $this->loadData();
    }

    public function createCollection(string $name): void
    {
        RequestCollection::create(['name' => $name]);
        $this->loadData();
    }

    public function loadRequest(string $id): void
    {
        $request = HttpClientRequest::find($id);
        if ($request) {
            $this->method = $request->method;
            $this->url = $request->url;
            $this->headers = $request->headers ?? [['key' => '', 'value' => '']];
            $this->queryParams = $request->query_params ?? [['key' => '', 'value' => '']];
            $this->body = $request->body ?? '';
            $this->body_type = $request->body_type;
            $this->requestName = $request->name;
        }
    }

    public function loadFromHistory(string $id): void
    {
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

    // Environment Management
    public function openEnvModal(?string $id = null): void
    {
        if ($id) {
            $env = Environment::find($id);
            $vars = [];
            if ($env->variables) {
                foreach ($env->variables as $k => $v) {
                    $vars[] = ['key' => $k, 'value' => $v];
                }
            }
            $this->editingEnv = [
                'id' => $id,
                'name' => $env->name,
                'variables' => empty($vars) ? [['key' => '', 'value' => '']] : $vars
            ];
        } else {
            $this->editingEnv = ['name' => '', 'variables' => [['key' => '', 'value' => '']]];
        }
        $this->showEnvModal = true;
    }

    public function addEnvVar(): void
    {
        $this->editingEnv['variables'][] = ['key' => '', 'value' => ''];
    }

    public function removeEnvVar(int $index): void
    {
        unset($this->editingEnv['variables'][$index]);
        $this->editingEnv['variables'] = array_values($this->editingEnv['variables']);
    }

    public function saveEnvironment(): void
    {
        $variables = collect($this->editingEnv['variables'])
            ->filter(fn($v) => !empty($v['key']))
            ->pluck('value', 'key')
            ->toArray();

        if (isset($this->editingEnv['id'])) {
            $env = Environment::find($this->editingEnv['id']);
            $env->update([
                'name' => $this->editingEnv['name'],
                'variables' => $variables
            ]);
        } else {
            Environment::create([
                'name' => $this->editingEnv['name'],
                'variables' => $variables
            ]);
        }

        $this->showEnvModal = false;
        $this->loadData();
    }

    public function deleteRequest(string $id): void
    {
        HttpClientRequest::destroy($id);
        $this->loadData();
    }

    public function deleteCollection(string $id): void
    {
        RequestCollection::destroy($id);
        $this->loadData();
    }

    public function duplicateRequest(string $id): void
    {
        $request = HttpClientRequest::find($id);
        if ($request) {
            $newRequest = $request->replicate();
            $newRequest->name = $request->name . ' (Copy)';
            $newRequest->save();
            $this->loadData();
        }
    }
};
?>

<div class="flex h-[calc(100vh-56px)] overflow-hidden bg-white text-gray-800 font-sans" x-data="{ sidebarTab: 'collections' }">
    <!-- Sidebar -->
    <aside class="w-72 bg-gray-50 border-r border-gray-200 flex flex-col shrink-0">
        <!-- Sidebar Tabs -->
        <div class="flex border-b border-gray-200 bg-white">
            <button @click="sidebarTab = 'collections'" :class="sidebarTab === 'collections' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'" class="flex-1 py-3 text-[10px] font-bold uppercase tracking-wider border-b-2 transition">Collections</button>
            <button @click="sidebarTab = 'history'" :class="sidebarTab === 'history' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'" class="flex-1 py-3 text-[10px] font-bold uppercase tracking-wider border-b-2 transition">History</button>
        </div>

        <!-- Collections Tab -->
        <div x-show="sidebarTab === 'collections'" class="flex-1 overflow-y-auto p-2">
            <div class="flex justify-between items-center px-2 mb-4 mt-2">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Your Collections</h3>
                <button wire:click="createCollection('New Collection')" class="text-blue-600 hover:text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>
            
            <div class="space-y-1">
                @forelse($collections as $collection)
                    <div x-data="{ open: true }" class="mb-2">
                        <div class="flex items-center group">
                            <button @click="open = !open" class="flex-1 flex items-center p-2 hover:bg-gray-200 rounded transition">
                                <svg :class="open ? 'rotate-90' : ''" class="w-3 h-3 mr-2 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <span class="text-xs font-bold text-gray-700 truncate">{{ $collection->name }}</span>
                            </button>
                            <button wire:click="deleteCollection('{{ $collection->id }}')" wire:confirm="Are you sure?" class="p-2 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                        <div x-show="open" class="ml-4 space-y-0.5 border-l border-gray-200 pl-2 mt-1">
                            @foreach($collection->requests as $req)
                                <div class="flex items-center group">
                                    <button wire:click="loadRequest('{{ $req->id }}')" class="flex-1 text-left p-1.5 hover:bg-gray-200 rounded text-[11px] text-gray-600 flex items-center">
                                        <span class="w-8 font-bold text-[9px] mr-2 {{ in_array($req->method, ['POST', 'PUT']) ? 'text-green-600' : 'text-blue-600' }}">{{ $req->method }}</span>
                                        <span class="truncate">{{ $req->name }}</span>
                                    </button>
                                    <div class="flex items-center opacity-0 group-hover:opacity-100 transition px-1">
                                        <button wire:click="duplicateRequest('{{ $req->id }}')" class="p-1 text-gray-400 hover:text-blue-600" title="Duplicate">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                        </button>
                                        <button wire:click="deleteRequest('{{ $req->id }}')" wire:confirm="Are you sure?" class="p-1 text-gray-400 hover:text-red-500" title="Delete">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-[11px] text-gray-400 text-center italic mt-10">No collections created</p>
                @endforelse
            </div>
        </div>

        <!-- History Tab -->
        <div x-show="sidebarTab === 'history'" class="flex-1 overflow-y-auto">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Recent Activity</h3>
                <button wire:click="clearHistory" class="text-gray-400 hover:text-red-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
            @if(empty($history))
                <div class="p-8 text-center text-gray-400 text-xs italic">No request history</div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($history as $item)
                        <button wire:click="loadFromHistory('{{ $item['id'] }}')" class="w-full text-left p-3 hover:bg-gray-200 transition group border-l-2 border-transparent hover:border-blue-500">
                            <div class="flex items-center space-x-2">
                                <span class="text-[9px] font-black px-1 py-0.5 rounded {{ in_array($item['method'], ['POST', 'PUT']) ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $item['method'] }}
                                </span>
                                <span class="text-[10px] text-gray-400">{{ $item['time'] }}</span>
                                <span class="text-[10px] font-bold ml-auto {{ $item['status'] < 400 ? 'text-green-500' : 'text-red-500' }}">{{ $item['status'] }}</span>
                            </div>
                            <div class="mt-1 text-[11px] text-gray-700 truncate font-mono">{{ $item['url'] }}</div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="flex-1 flex flex-col min-w-0 bg-white shadow-inner">
        <!-- Top Bar: Environment & Actions -->
        <div class="h-14 border-b border-gray-200 px-6 flex items-center justify-between bg-white shrink-0">
            <div class="flex items-center space-x-4">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Environment:</span>
                <div class="flex items-center">
                    <select wire:model.live="selectedEnvironmentId" class="bg-gray-100 border-none rounded text-xs font-bold p-1.5 focus:ring-2 focus:ring-blue-500 min-w-[150px]">
                        <option value="">No Environment</option>
                        @foreach($environments as $env)
                            <option value="{{ $env->id }}">{{ $env->name }}</option>
                        @endforeach
                    </select>
                    <button wire:click="openEnvModal(selectedEnvironmentId)" class="ml-2 text-gray-400 hover:text-blue-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </button>
                    <button wire:click="openEnvModal()" class="ml-1 text-gray-400 hover:text-green-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button wire:click="openSaveModal" class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded transition border border-gray-200">SAVE</button>
            </div>
        </div>

        <!-- Request Composer -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex space-x-2">
                <div class="flex-1 flex border border-gray-300 rounded overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-blue-500">
                    <select wire:model="method" class="bg-gray-50 border-r border-gray-300 text-gray-700 font-black text-[11px] px-4 focus:ring-0">
                        <option>GET</option>
                        <option>POST</option>
                        <option>PUT</option>
                        <option>PATCH</option>
                        <option>DELETE</option>
                        <option>OPTIONS</option>
                    </select>
                    <input wire:model="url" type="text" placeholder="https://api.example.com/v1/{{resourceId}}" class="flex-1 border-none text-sm font-mono focus:ring-0 px-4">
                </div>
                <button wire:click="sendRequest" wire:loading.attr="disabled" class="bg-blue-600 text-white px-8 py-2 rounded font-black text-xs hover:bg-blue-700 transition shadow-lg flex items-center">
                    <span wire:loading.remove>SEND</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        SENDING
                    </span>
                </button>
            </div>
        </div>

        <!-- Request Details -->
        <div class="flex-1 flex flex-col min-h-0" x-data="{ activeTab: 'params' }">
            <div class="px-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'params'" :class="activeTab === 'params' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 px-1 border-b-2 font-black text-[10px] uppercase tracking-widest transition">Parameters</button>
                    <button @click="activeTab = 'headers'" :class="activeTab === 'headers' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 px-1 border-b-2 font-black text-[10px] uppercase tracking-widest transition">Headers</button>
                    <button @click="activeTab = 'body'" :class="activeTab === 'body' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 px-1 border-b-2 font-black text-[10px] uppercase tracking-widest transition">Body</button>
                </nav>
            </div>

            <div class="flex-1 overflow-y-auto p-6 font-mono text-xs">
                <!-- Params Editor -->
                <div x-show="activeTab === 'params'" class="space-y-3">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-[9px] text-gray-400 font-black uppercase tracking-[2px]">Query Parameters</h4>
                        <button wire:click="addQueryParam" class="text-blue-600 hover:text-blue-700 text-[10px] font-black">+ ADD</button>
                    </div>
                    @foreach($queryParams as $index => $param)
                        <div class="flex space-x-2 group">
                            <input wire:model="queryParams.{{ $index }}.key" type="text" placeholder="Key" class="flex-1 border-gray-200 rounded text-xs p-2 focus:border-blue-500 focus:ring-0">
                            <input wire:model="queryParams.{{ $index }}.value" type="text" placeholder="Value" class="flex-1 border-gray-200 rounded text-xs p-2 focus:border-blue-500 focus:ring-0">
                            <button wire:click="removeQueryParam({{ $index }})" class="p-2 text-gray-300 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Headers Editor -->
                <div x-show="activeTab === 'headers'" class="space-y-3">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-[9px] text-gray-400 font-black uppercase tracking-[2px]">Request Headers</h4>
                        <button wire:click="addHeader" class="text-blue-600 hover:text-blue-700 text-[10px] font-black">+ ADD</button>
                    </div>
                    @foreach($headers as $index => $header)
                        <div class="flex space-x-2 group">
                            <input wire:model="headers.{{ $index }}.key" type="text" placeholder="Header" class="flex-1 border-gray-200 rounded text-xs p-2 focus:border-blue-500 focus:ring-0">
                            <input wire:model="headers.{{ $index }}.value" type="text" placeholder="Value" class="flex-1 border-gray-200 rounded text-xs p-2 focus:border-blue-500 focus:ring-0">
                            <button wire:click="removeHeader({{ $index }})" class="p-2 text-gray-300 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Body Editor -->
                <div x-show="activeTab === 'body'" class="h-full flex flex-col">
                    <div class="flex items-center space-x-4 mb-4 text-[10px] font-bold text-gray-400">
                        <label class="flex items-center space-x-1 cursor-pointer">
                            <input type="radio" wire:model="body_type" value="json" class="text-blue-600 focus:ring-0">
                            <span>JSON</span>
                        </label>
                    </div>
                    <textarea wire:model="body" class="flex-1 w-full border border-gray-200 rounded p-4 text-xs font-mono focus:border-blue-500 focus:ring-0 resize-none min-h-[200px]" placeholder='{ "key": "value" }'></textarea>
                </div>
            </div>

            <!-- Response Pane -->
            <div class="h-1/2 border-t border-gray-200 flex flex-col bg-gray-50 overflow-hidden">
                <div class="px-6 py-2 border-b border-gray-200 bg-white flex justify-between items-center shrink-0">
                    <h3 class="font-black text-[9px] uppercase tracking-[2px] text-gray-400">Response</h3>
                    @if($response && !isset($response['error']))
                        <div class="flex space-x-6 text-[10px] font-black">
                            <span class="{{ $response['status'] < 400 ? 'text-green-600' : 'text-red-600' }}">STATUS: {{ $response['status'] }}</span>
                            <span class="text-gray-400">TIME: {{ $response['duration'] }}ms</span>
                            <span class="text-gray-400">SIZE: {{ number_format($response['size'] / 1024, 2) }}KB</span>
                        </div>
                    @endif
                </div>
                
                <div class="flex-1 overflow-hidden flex flex-col" x-data="{ resTab: 'body' }">
                    @if($response)
                        @if(isset($response['error']))
                            <div class="p-12 text-center">
                                <div class="inline-block p-4 bg-red-100 border border-red-200 rounded text-red-700 text-xs font-bold">
                                    {{ $response['error'] }}
                                </div>
                            </div>
                        @else
                            <div class="px-6 border-b border-gray-200 bg-white">
                                <nav class="-mb-px flex space-x-6">
                                    <button @click="resTab = 'body'" :class="resTab === 'body' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-black text-[9px] uppercase tracking-widest transition">Body</button>
                                    <button @click="resTab = 'headers'" :class="resTab === 'headers' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-black text-[9px] uppercase tracking-widest transition">Headers</button>
                                </nav>
                            </div>
                            <div class="flex-1 overflow-y-auto bg-gray-900">
                                <div x-show="resTab === 'body'" class="h-full relative group">
                                    <button onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText)" class="absolute top-4 right-4 bg-gray-800 text-gray-400 hover:text-white p-2 rounded opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 012-2v-8a2 2 0 01-2-2h-8a2 2 0 01-2 2v8a2 2 0 012 2z"></path></svg>
                                    </button>
                                    <pre class="p-6 text-green-400 whitespace-pre-wrap font-mono text-xs"><code>{{ is_array(json_decode($response['content'], true)) ? json_encode(json_decode($response['content'], true), JSON_PRETTY_PRINT) : $response['content'] }}</code></pre>
                                </div>
                                <div x-show="resTab === 'headers'" class="p-6 space-y-2 font-mono text-[11px]">
                                    @foreach($response['headers'] as $name => $values)
                                        <div class="flex">
                                            <span class="text-blue-400 font-bold shrink-0 w-40">{{ is_array($name) ? implode(', ', $name) : $name }}:</span>
                                            <span class="text-gray-400 ml-4 break-all">{{ is_array($values) ? implode(', ', $values) : $values }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center text-gray-200">
                            <svg class="w-20 h-20 mb-6 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <p class="text-xs font-black uppercase tracking-[3px] opacity-30 italic">Ready for Request</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    @if($showEnvModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-lg shadow-2xl w-[600px] flex flex-col max-h-[80vh]">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-black text-xs uppercase tracking-widest text-gray-500">{{ isset($editingEnv['id']) ? 'Edit' : 'New' }} Environment</h3>
                    <button wire:click="$set('showEnvModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto">
                    <div class="mb-6">
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-2">Environment Name</label>
                        <input wire:model="editingEnv.name" type="text" class="w-full border-gray-200 rounded p-2 text-sm focus:border-blue-500 focus:ring-0" placeholder="e.g. Production">
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400">Variables</label>
                            <button wire:click="addEnvVar" class="text-blue-600 text-[10px] font-black">+ ADD VARIABLE</button>
                        </div>
                        <div class="space-y-3">
                            @foreach($editingEnv['variables'] as $index => $var)
                                <div class="flex space-x-2 group">
                                    <input wire:model="editingEnv.variables.{{ $index }}.key" type="text" placeholder="Key" class="flex-1 border-gray-200 rounded text-xs p-2 focus:border-blue-500 focus:ring-0">
                                    <input wire:model="editingEnv.variables.{{ $index }}.value" type="text" placeholder="Value" class="flex-1 border-gray-200 rounded text-xs p-2 focus:border-blue-500 focus:ring-0">
                                    <button wire:click="removeEnvVar({{ $index }})" class="p-2 text-gray-300 hover:text-red-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-between bg-gray-50 rounded-b-lg">
                    @if(isset($editingEnv['id']))
                        <button wire:click="deleteEnvironment('{{ $editingEnv['id'] }}')" class="text-red-500 text-[10px] font-black uppercase tracking-widest hover:text-red-700">Delete</button>
                    @else
                        <span></span>
                    @endif
                    <div class="space-x-4">
                        <button wire:click="$set('showEnvModal', false)" class="text-[10px] font-black uppercase tracking-widest text-gray-400">Cancel</button>
                        <button wire:click="saveEnvironment" class="bg-blue-600 text-white px-6 py-2 rounded text-[10px] font-black uppercase tracking-widest shadow-lg">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showSaveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-lg shadow-2xl w-96 p-6">
                <h3 class="font-black text-xs uppercase tracking-widest text-gray-500 mb-6">Save Request</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-2">Request Name</label>
                        <input wire:model="requestName" type="text" class="w-full border-gray-200 rounded p-2 text-sm focus:border-blue-500 focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-2">Collection</label>
                        <select wire:model="selectedCollectionId" class="w-full border-gray-200 rounded p-2 text-sm focus:border-blue-500 focus:ring-0">
                            <option value="">Select a collection</option>
                            @foreach($collections as $col)
                                <option value="{{ $col->id }}">{{ $col->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-4">
                    <button wire:click="$set('showSaveModal', false)" class="text-[10px] font-black uppercase tracking-widest text-gray-400">Cancel</button>
                    <button wire:click="saveRequest" class="bg-blue-600 text-white px-6 py-2 rounded text-[10px] font-black uppercase tracking-widest shadow-lg">Save</button>
                </div>
            </div>
        </div>
    @endif
</div>
