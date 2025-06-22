@extends('admin.layout.master', [
    'title' => 'Add Medical Center',
])

@section('content')
    <div class="v-cloak--hidden py-6 flex flex-col gap-6">
        <div class="px-6">
            <div class="flex items-center justify-between">
                <h1>Add Medical Center</h1>
                <a href="{{ route('admin.centers.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-secondary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-secondary-700 focus:bg-secondary-700 active:bg-secondary-900 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fad fa-arrow-left mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>

        <div class="px-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Medical Center Information</h2>
                    <p class="mt-1 text-sm text-gray-600">Add new nuclear medicine medical center basic information and service settings.</p>
                </div>

                <form method="POST" action="{{ route('admin.centers.store') }}" class="px-6 py-4">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Medical Center Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Medical Center Name</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code Name -->
                        <div>
                            <label for="code_name" class="block text-sm font-medium text-gray-700">Code Name</label>
                            <input type="text" 
                                   name="code_name" 
                                   id="code_name" 
                                   value="{{ old('code_name') }}"
                                   required
                                   maxlength="10"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('code_name') border-red-300 @enderror">
                            @error('code_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State/Region -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">State/Region</label>
                            <select name="state" 
                                    id="state" 
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('state') border-red-300 @enderror">
                                <option value="">Select State/Region</option>
                                <option value="W.P. Kuala Lumpur" {{ old('state') == 'W.P. Kuala Lumpur' ? 'selected' : '' }}>Kuala Lumpur</option>
                                <option value="Selangor" {{ old('state') == 'Selangor' ? 'selected' : '' }}>Selangor</option>
                                <option value="Pulau Pinang" {{ old('state') == 'Pulau Pinang' ? 'selected' : '' }}>Penang</option>
                                <option value="Johor" {{ old('state') == 'Johor' ? 'selected' : '' }}>Johor</option>
                                <option value="Perak" {{ old('state') == 'Perak' ? 'selected' : '' }}>Perak</option>
                                <option value="Sarawak" {{ old('state') == 'Sarawak' ? 'selected' : '' }}>Sarawak</option>
                                <option value="Sabah" {{ old('state') == 'Sabah' ? 'selected' : '' }}>Sabah</option>
                                <option value="Kelantan" {{ old('state') == 'Kelantan' ? 'selected' : '' }}>Kelantan</option>
                                <option value="Pahang" {{ old('state') == 'Pahang' ? 'selected' : '' }}>Pahang</option>
                                <option value="Melaka" {{ old('state') == 'Melaka' ? 'selected' : '' }}>Melaka</option>
                                <option value="W.P. Putrajaya" {{ old('state') == 'W.P. Putrajaya' ? 'selected' : '' }}>Putrajaya</option>
                            </select>
                            @error('state')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code Number -->
                        <div>
                            <label for="code_no" class="block text-sm font-medium text-gray-700">Code Number</label>
                            <input type="text" 
                                   name="code_no" 
                                   id="code_no" 
                                   value="{{ old('code_no') }}"
                                   required
                                   maxlength="10"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('code_no') border-red-300 @enderror">
                            @error('code_no')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Information -->
                        <div class="sm:col-span-2">
                            <label for="contact" class="block text-sm font-medium text-gray-700">Contact Information</label>
                            <textarea name="contact" 
                                      id="contact" 
                                      rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('contact') border-red-300 @enderror"
                                      placeholder="Phone, email and other contact information">{{ old('contact') }}</textarea>
                            @error('contact')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Official Website -->
                        <div class="sm:col-span-2">
                            <label for="webpage" class="block text-sm font-medium text-gray-700">Official Website</label>
                            <input type="url" 
                                   name="webpage" 
                                   id="webpage" 
                                   value="{{ old('webpage') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('webpage') border-red-300 @enderror"
                                   placeholder="https://example.com">
                            @error('webpage')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Services Provided -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4">Services Provided</label>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                            @php
                                $services = [
                                    'SPECT' => 'SPECT Scan',
                                    'PET' => 'PET Scan',
                                    'RAI' => 'Iodine-131 Treatment',
                                    'PRRT' => 'PRRT Treatment',
                                    'PSMA' => 'PSMA Treatment',
                                    'SIRT' => 'SIRT Treatment',
                                    'MIBG' => 'MIBG Treatment',
                                    'BONE-P' => 'Bone Pain Treatment',
                                    'RSO' => 'RSO Treatment',
                                    'AC' => 'AC Treatment',
                                ];
                                $selectedServices = old('services', []);
                            @endphp
                            @foreach($services as $code => $label)
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="services[]" 
                                       id="service_{{ $code }}" 
                                       value="{{ $code }}"
                                       {{ (collect($selectedServices)->contains($code)) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="service_{{ $code }}" class="ml-2 block text-sm text-gray-700">
                                    {{ $label }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('services')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="mt-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                Active Status
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Newly added medical centers are activated by default. Inactive medical centers will not be displayed in the system.</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.centers.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fad fa-plus mr-2"></i>
                            Create Medical Center
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 