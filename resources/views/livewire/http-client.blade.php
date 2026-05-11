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
    
    public ?array $response = null;
    public bool $loading = false;

    public function addHeader(): void
    {
        $this->headers[] = ['key' => '', 'value' => ''];
    }

    public function removeHeader(int $index): void
    {
        unset($this->headers[$index]);
        $this->headers = array_values($this->headers);
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

            $startTime = microtime(true);
            
            $request = Http::withHeaders($preparedHeaders);
            
            if ($this->method === 'GET') {
                $res = $request->get($this->url);
            } elseif ($this->method === 'POST') {
                $res = $request->withBody($this->body, 'application/json')->post($this->url);
            } elseif ($this->method === 'PUT') {
                $res = $request->withBody($this->body, 'application/json')->put($this->url);
            } elseif ($this->method === 'DELETE') {
                $res = $request->delete($this->url);
            } else {
                $res = $request->send($this->method, $this->url);
            }

            $duration = round((microtime(true) - $startTime) * 1000);

            $this->response = [
                'status' => $res->status(),
                'headers' => $res->headers(),
                'content' => $res->body(),
                'duration' => $duration,
                'size' => strlen($res->body()),
            ];
        } catch (\Exception $e) {
            $this->response = [
                'error' => $e->getMessage(),
            ];
        }

        $this->loading = false;
    }
};
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex space-x-2">
            <select wire:model="method" class="w-32 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option>GET</option>
                <option>POST</option>
                <option>PUT</option>
                <option>PATCH</option>
                <option>DELETE</option>
                <option>OPTIONS</option>
                <option>HEAD</option>
            </select>
            <input wire:model="url" type="text" placeholder="https://api.example.com/endpoint" class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button wire:click="sendRequest" wire:loading.attr="disabled" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 flex items-center">
                <span wire:loading.remove>Send</span>
                <span wire:loading>Sending...</span>
            </button>
        </div>

        <div class="mt-6" x-data="{ tab: 'headers' }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button @click="tab = 'headers'" :class="tab === 'headers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Headers</button>
                    <button @click="tab = 'body'" :class="tab === 'body' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Body</button>
                </nav>
            </div>

            <div x-show="tab === 'headers'" class="mt-4 space-y-2">
                @foreach($headers as $index => $header)
                    <div class="flex space-x-2">
                        <input wire:model="headers.{{ $index }}.key" type="text" placeholder="Key" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm">
                        <input wire:model="headers.{{ $index }}.value" type="text" placeholder="Value" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm">
                        <button wire:click="removeHeader({{ $index }})" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @endforeach
                <button wire:click="addHeader" class="text-sm text-blue-600 hover:underline">+ Add Header</button>
            </div>

            <div x-show="tab === 'body'" class="mt-4">
                <textarea wire:model="body" rows="8" class="w-full border-gray-300 rounded-md shadow-sm font-mono text-sm" placeholder='{"key": "value"}'></textarea>
            </div>
        </div>
    </div>

    @if($response)
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            @if(isset($response['error']))
                <div class="p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                    <p class="font-bold">Error</p>
                    <p>{{ $response['error'] }}</p>
                </div>
            @else
                <div class="flex justify-between items-center mb-4">
                    <div class="flex space-x-4 text-sm">
                        <span class="font-bold {{ $response['status'] < 400 ? 'text-green-600' : 'text-red-600' }}">Status: {{ $response['status'] }}</span>
                        <span class="text-gray-500">Time: {{ $response['duration'] }} ms</span>
                        <span class="text-gray-500">Size: {{ number_format($response['size'] / 1024, 2) }} KB</span>
                    </div>
                </div>

                <div x-data="{ resTab: 'body' }">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="resTab = 'body'" :class="resTab === 'body' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Response Body</button>
                            <button @click="resTab = 'headers'" :class="resTab === 'headers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Response Headers</button>
                        </nav>
                    </div>

                    <div x-show="resTab === 'body'" class="mt-4">
                        <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm font-mono max-h-96"><code>{{ $response['content'] }}</code></pre>
                    </div>

                    <div x-show="resTab === 'headers'" class="mt-4">
                        <div class="bg-gray-50 p-4 rounded-lg space-y-1">
                            @foreach($response['headers'] as $name => $values)
                                <div class="text-sm">
                                    <span class="font-bold text-gray-700">{{ is_array($name) ? implode(', ', $name) : $name }}:</span>
                                    <span class="text-gray-600">{{ is_array($values) ? implode(', ', $values) : $values }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
