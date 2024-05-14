@extends('layouts.app.main')

@section('title', ' | Log Synchronize')

@section('content')
<h3 class="text-gray-700 text-3xl font-medium">Log Synchronize</h3>
<div class="container bg-white p-10 my-10" x-data="dataCrud()">
    {{-- <button class="relative bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="livewire.emit('refreshUser')">Refresh Table</button> --}}
    <div x-show="successAlert.open" class="relative py-3 pl-4 pr-10 leading-normal text-blue-700 bg-blue-100 rounded-lg mb-3" role="alert">
        <p x-text="successAlert.message">A simple alert with text and a right icon</p>
        <span class="absolute inset-y-0 right-0 flex items-center mr-4" @click="successAlert.open = false">
          <svg class="w-4 h-4 fill-current" role="button" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
        </span>
    </div>
    <x-modal :value="1">
        <x-slot name="trigger">
            
        </x-slot>
        
        <!-- Title -->
        <div class="border-b-2 border-black mb-4">
            <h2 class="text-3xl font-medium" :id="$id('modal-title')">Log Data</h2>
        </div>
        <!-- Content -->
        <form action="#" @submit.prevent="confirmSave">
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full px-3 my-3">
                    <label class="block tracking-wide text-gray-700 text-sm font-bold mb-2" for="table">
                        Data
                    </label>
                    <input name="table" x-model="form.table"
                    class="w-full h-10 px-3 text-base text-gray-700 placeholder-gray-300 border rounded-lg focus:shadow-outline" 
                    type="text" readonly>
                </div>
                <div class="w-full px-3 my-3">
                    <label class="block tracking-wide text-gray-700 text-sm font-bold mb-2" for="params">
                        Param
                    </label>
                    <textarea name="params" x-model="form.params"
                    class="w-full h-24 px-3 text-base text-gray-700 placeholder-gray-300 border rounded-lg focus:shadow-outline" 
                    type="text" readonly ></textarea>
                </div>
                <div class="w-full px-3 my-3">
                    <label class="block tracking-wide text-gray-700 text-sm font-bold mb-2" for="status_result">
                        Status
                    </label>
                    <input name="status_result" x-model="form.status_result"
                    class="w-full h-10 px-3 text-base text-gray-700 placeholder-gray-300 border rounded-lg focus:shadow-outline" 
                    type="text" readonly>
                </div>
                <div class="w-full px-3 my-3">
                    <label class="block tracking-wide text-gray-700 text-sm font-bold mb-2" for="msg_result">
                        Message
                    </label>
                    <textarea name="msg_result" x-model="form.msg_result"
                    class="w-full h-24 px-3 text-base text-gray-700 placeholder-gray-300 border rounded-lg focus:shadow-outline" 
                    type="text" readonly ></textarea>
                </div>
                <div class="w-full px-3 my-3">
                    <label class="block tracking-wide text-gray-700 text-sm font-bold mb-2" for="extra_info">
                        Extra Info
                    </label>
                    <textarea name="extra_info" x-model="form.extra_info"
                    class="w-full h-24 px-3 text-base text-gray-700 placeholder-gray-300 border rounded-lg focus:shadow-outline" 
                    type="text" readonly ></textarea>
                </div>
                <div class="w-full px-3 my-3">
                    <label class="block tracking-wide text-gray-700 text-sm font-bold mb-2" for="created_by">
                        Author
                    </label>
                    <input name="created_by" x-model="form.created_by"
                    class="w-full h-10 px-3 text-base text-gray-700 placeholder-gray-300 border rounded-lg focus:shadow-outline" 
                    type="text" readonly>
                </div>
                <div class="w-full px-3 my-3">
                    <label class="block tracking-wide text-gray-700 text-sm font-bold mb-2" for="created_at">
                        Created at
                    </label>
                    <input name="created_at" x-model="form.created_at"
                    class="w-full h-10 px-3 text-base text-gray-700 placeholder-gray-300 border rounded-lg focus:shadow-outline" 
                    type="text" readonly>
                </div>                
            </div>
            <!-- Buttons -->
            <div class="mt-8 flex space-x-2 justify-end">
                <button type="button" x-on:click="openModal = false"  class="relative bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mb-2 focus:outline-none focus:ring-4 focus:ring-aqua-400">
                    <i class="fa fa-times-circle"></i>
                    Cancel
                </button>
                <a :href="`{{ env('APP_URL') }}/data/logsynchronize/details/${idData}`" target="_blank"
                    class="relative text-white font-bold py-2 px-4 rounded mb-2 focus:outline-none focus:ring-4 focus:ring-aqua-400 bg-blue-500 hover:bg-blue-700" type="button"
                >
                    <i class="fa fa-search"></i> Lihat Data Mentah
                </a>
            </div>
        </form>
                
    </x-modal>
    

    <!-- ini datatable -->
    <x-app.datatable.datatable>
        <x-slot:thead>
            <tr>
                <th scope="col" class="px-4 py-3">No</th>
                <th scope="col" class="px-4 py-3">Data</th>
                <th scope="col" class="px-4 py-3">Params</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3">Message</th>
                <th scope="col" class="px-4 py-3">Author</th>
                <th scope="col" class="px-4 py-3">Created at</th>
                <th scope="col" class="px-4 py-3">
                    <span class="sr-only">Actions</span>
                </th>
            </tr>
        </x-slot:thead>
        <x-slot:tbody>
            <template x-for="(row, index) in datatable.data" :key="index">
                <tr x-show="!datatable.loading" class="border-b dark:border-gray-700">
                    <td class="px-4 py-3" x-text="datatable.numbering(index)"></td>
                    <td class="px-4 py-3" x-text="row.table"></td>
                    <td class="px-4 py-3" x-text="row.params"></td>
                    <td class="px-4 py-3" x-text="row.status_result"></td>
                    <td class="px-4 py-3" x-text="row.msg_result"></td>
                    <td class="px-4 py-3" x-text="row.created_by"></td>
                    <td class="px-4 py-3" x-text="row.created_at"></td>
                    <td class="px-4 py-3 flex items-center justify-end">
                        <button 
                        :disabled="loadingState"
                        class="inline-flex items-center justify-center w-8 h-8 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-full focus:shadow-outline hover:bg-indigo-800 
                                disabled:cursor-wait disabled:bg-indigo-800" @click="editData(row.id)">
                            <template x-if="!loadingState">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>                                  
                            </template>
                            <template x-if="loadingState">
                                <i class="fa fa-spinner animate-spin"></i>
                            </template>
                        </button>
                        
                    </td>
                </tr>
            </template>
        </x-slot:tbody>
    </x-app.datatable.datatable>
    <!-- ini datatable -->
</div>
@endsection

<!-- Your Custom Javascript -->
@section('_inJs')
@include('app.data.logsynchronize._inJs')
@endsection
<!-- /Your Custom Javascript -->

