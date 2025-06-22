<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nucleo Map iframe Embed Example</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon-64x64.png') }}">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8fafc;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #6b7280;
            font-size: 18px;
        }
        
        .map-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        
        .map-section h2 {
            color: #374151;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        
        .map-section p {
            color: #6b7280;
            margin-bottom: 20px;
        }
        
        .map-iframe {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            width: 100%;
            height: 600px;
            display: block;
        }
        
        .controls {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .controls button {
            background: #6366f1;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        .controls button:hover {
            background: #4f46e5;
        }
        
        .url-display {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 12px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 14px;
            color: #374151;
            word-break: break-all;
            margin-bottom: 15px;
        }
        
        .responsive-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .nav-links {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .nav-links a {
            display: inline-block;
            margin: 0 15px;
            padding: 10px 20px;
            background: #6366f1;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        
        .nav-links a:hover {
            background: #4f46e5;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .responsive-grid {
                grid-template-columns: 1fr;
            }
            
            .controls {
                flex-direction: column;
            }
            
            .controls button {
                width: 100%;
            }
            
            .nav-links a {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
                    <h1>🗺️ Nucleo Map iframe Embed Example</h1>
        <p>Below shows how to embed Nucleo Map in a webpage</p>
        </div>

        <!-- Navigation links -->
        <div class="nav-links">
            <a href="{{ route('map.index') }}" target="_blank">Full Map Page</a>
            <a href="{{ route('map.embed') }}" target="_blank">Embedded Map</a>
            <a href="{{ url('/') }}">Back to Home</a>
        </div>

        <!-- Basic embed example -->
        <div class="map-section">
            <h2>📍 Basic Map Embed</h2>
            <p>The simplest embedding method, displaying all medical centers:</p>
            
            <div class="url-display" id="basic-url">
                {{ route('map.embed') }}
            </div>
            
            <iframe 
                id="basic-map"
                src="{{ route('map.embed') }}" 
                class="map-iframe"
                frameborder="0"
                allowfullscreen>
            </iframe>
        </div>

        <!-- Embed with filters example -->
        <div class="map-section">
            <h2>🔍 Embed with Filter Parameters</h2>
            <p>Use URL parameters to preset filter conditions:</p>
            
            <div class="controls">
                            <button onclick="loadFilteredMap('Selangor', 'PET', '')">Selangor PET Centers</button>
            <button onclick="loadFilteredMap('Johor', 'SPECT', '')">Johor SPECT Centers</button>
            <button onclick="loadFilteredMap('', 'RAI', '')">All RAI Treatment Centers</button>
            <button onclick="loadFilteredMap('', '', 'hospital')">Search "hospital"</button>
            <button onclick="loadFilteredMap('', '', '')">Reset Filters</button>
            </div>
            
            <div class="url-display" id="filtered-url">
                {{ route('map.embed') }}
            </div>
            
            <iframe 
                id="filtered-map"
                src="{{ route('map.embed') }}" 
                class="map-iframe"
                frameborder="0"
                allowfullscreen>
            </iframe>
        </div>

        <!-- Responsive embed example -->
        <div class="responsive-grid">
            <div class="map-section">
                <h2>📱 Mobile Optimized</h2>
                <p>Suitable smaller size for mobile:</p>
                
                <iframe 
                    src="{{ route('map.embed', ['state' => 'Penang']) }}" 
                    style="width: 100%; height: 400px; border: 2px solid #e5e7eb; border-radius: 8px;"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>

            <div class="map-section">
                <h2>🖥️ Desktop Optimized</h2>
                <p>Full features suitable for desktop:</p>
                
                <iframe 
                    src="{{ route('map.embed', ['service' => 'PRRT']) }}" 
                    style="width: 100%; height: 500px; border: 2px solid #e5e7eb; border-radius: 8px;"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>
        </div>

        <!-- API Data Display -->
        <div class="map-section" style="margin-top: 40px;">
            <h2>📊 API Data Overview</h2>
            <p>Below shows all medical center data loaded from API:</p>
            
            <div class="controls">
                <button onclick="loadApiData()">Load API Data</button>
                <button onclick="loadFilterData()">Load Filter Options</button>
                <button onclick="clearApiData()">Clear Data</button>
            </div>
            
            <div id="api-loading" style="display: none; text-align: center; padding: 20px;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #e5e7eb; border-top: 4px solid #6366f1; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 10px; color: #6b7280;">Loading...</p>
            </div>
            
            <div id="api-data" style="margin-top: 20px;"></div>
            <div id="filter-data" style="margin-top: 20px;"></div>
        </div>

        <!-- Usage Instructions -->
        <div class="map-section" style="margin-top: 40px;">
            <h2>📖 Usage Instructions</h2>
            
            <h3>Supported URL Parameters:</h3>
            <ul>
                <li><strong>state</strong>: State filter (e.g.: Selangor, Johor, Penang)</li>
                <li><strong>service</strong>: Service type (e.g.: PET, SPECT, RAI, PRRT)</li>
                <li><strong>keyword</strong>: Keyword search (e.g.: hospital, clinic)</li>
            </ul>

            <h3>Complete URL Examples:</h3>
            <div class="url-display">
{{ route('map.index') }}
{{ route('map.embed') }}
{{ route('map.embed') }}?state=Selangor&service=PET
{{ route('map.embed') }}?keyword=hospital&state=Johor
            </div>

            <h3>WordPress Shortcode:</h3>
            <div class="url-display">
[nucleo_map]
[nucleo_map height="500"]
[nucleo_map state="Selangor" service="PET"]
[nucleo_map state="Johor" keyword="hospital" height="700"]
            </div>

            <h3>HTML Embed Code:</h3>
            <div class="url-display">
&lt;iframe 
    src="{{ route('map.embed') }}?state=Selangor&amp;service=PET" 
    width="100%" 
    height="600"
    frameborder="0"
    allowfullscreen&gt;
&lt;/iframe&gt;
            </div>

            <h3>Laravel Blade Syntax:</h3>
            <div class="url-display">
&lt;iframe 
    src="@{{ route('map.embed', ['state' => 'Selangor', 'service' => 'PET']) }}" 
    width="100%" 
    height="600"
    frameborder="0"
    allowfullscreen&gt;
&lt;/iframe&gt;
            </div>
        </div>
    </div>

    <script>
        // Function to update filtered map
        function loadFilteredMap(state, service, keyword) {
            const baseUrl = '{{ route("map.embed") }}';
            const params = new URLSearchParams();
            
            if (state) params.append('state', state);
            if (service) params.append('service', service);
            if (keyword) params.append('keyword', keyword);
            
            const fullUrl = params.toString() ? 
                `${baseUrl}?${params.toString()}` : baseUrl;
            
            // Update iframe src
            document.getElementById('filtered-map').src = fullUrl;
            
            // Update displayed URL
            document.getElementById('filtered-url').textContent = fullUrl;
        }

        // Load API data
        async function loadApiData() {
            const loadingEl = document.getElementById('api-loading');
            const dataEl = document.getElementById('api-data');
            
            try {
                loadingEl.style.display = 'block';
                dataEl.innerHTML = '';
                
                const response = await fetch('{{ url("/api/public/centers/map-data") }}');
                const data = await response.json();
                
                if (data.status === 'success') {
                    displayCentersData(data.data);
                } else {
                    throw new Error(data.message || 'Load failed');
                }
            } catch (error) {
                console.error('Failed to load API data:', error);
                dataEl.innerHTML = `
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 15px; color: #dc2626;">
                        <strong>❌ Load failed:</strong> ${error.message}
                    </div>
                `;
            } finally {
                loadingEl.style.display = 'none';
            }
        }

        // Load filter options data
        async function loadFilterData() {
            const loadingEl = document.getElementById('api-loading');
            const dataEl = document.getElementById('filter-data');
            
            try {
                loadingEl.style.display = 'block';
                dataEl.innerHTML = '';
                
                const response = await fetch('{{ url("/api/public/centers/filters") }}');
                const data = await response.json();
                
                if (data.status === 'success') {
                    displayFiltersData(data.data);
                } else {
                    throw new Error(data.message || 'Load failed');
                }
            } catch (error) {
                console.error('Failed to load filter data:', error);
                dataEl.innerHTML = `
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 15px; color: #dc2626;">
                        <strong>❌ Load failed:</strong> ${error.message}
                    </div>
                `;
            } finally {
                loadingEl.style.display = 'none';
            }
        }

        // Display medical center data
        function displayCentersData(data) {
            const dataEl = document.getElementById('api-data');
            const centers = data.centers || [];
            
            let html = `
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                    <h3 style="margin: 0 0 10px 0; color: #15803d;">✅ Successfully loaded ${centers.length} medical centers</h3>
                    <p style="margin: 0; color: #166534;">
                        <strong>Total:</strong> ${data.total_count || centers.length} centers<br>
                        <strong>States:</strong> ${data.states ? data.states.length : 0}<br>
                        <strong>Service Types:</strong> ${data.services ? data.services.length : 0}
                    </p>
                </div>
                
                <div style="max-height: 600px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 6px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead style="background: #f9fafb; position: sticky; top: 0;">
                            <tr>
                                <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">ID</th>
                                                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Code</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">State</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Services</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Coordinates</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Contact</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            centers.forEach((center, index) => {
                const services = center.services ? center.services.join(', ') : 'N/A';
                const coordinates = center.coordinates ? 
                    `${center.coordinates.lat.toFixed(4)}, ${center.coordinates.lng.toFixed(4)}` : 'N/A';
                const contact = center.contact ? 
                    center.contact.replace(/\n/g, '<br>').substring(0, 100) + (center.contact.length > 100 ? '...' : '') : 'N/A';
                
                html += `
                    <tr style="border-bottom: 1px solid #f3f4f6; ${index % 2 === 0 ? 'background: #fafafa;' : ''}">
                        <td style="padding: 12px; font-weight: bold; color: #6366f1;">${center.id}</td>
                        <td style="padding: 12px;">
                            <div style="font-weight: 500; color: #1f2937;">${center.name}</div>
                            ${center.webpage ? `<a href="${center.webpage}" target="_blank" style="color: #6366f1; text-decoration: none; font-size: 12px;">🔗 Website</a>` : ''}
                        </td>
                        <td style="padding: 12px;">
                            <div style="font-family: monospace; background: #f3f4f6; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                                ${center.code_name || 'N/A'}
                            </div>
                            <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">${center.code_no || ''}</div>
                        </td>
                        <td style="padding: 12px;">
                            <span style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                ${center.state}
                            </span>
                        </td>
                        <td style="padding: 12px;">
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                ${center.services ? center.services.map(service => 
                                    `<span style="background: #dcfce7; color: #166534; padding: 1px 6px; border-radius: 8px; font-size: 11px;">${service}</span>`
                                ).join('') : 'N/A'}
                            </div>
                        </td>
                        <td style="padding: 12px; font-family: monospace; font-size: 12px; color: #6b7280;">
                            ${coordinates}
                        </td>
                        <td style="padding: 12px; font-size: 12px; color: #6b7280; max-width: 200px;">
                            ${contact}
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            dataEl.innerHTML = html;
        }

        // Display filter options data
        function displayFiltersData(data) {
            const dataEl = document.getElementById('filter-data');
            
            let html = `
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                    <h3 style="margin: 0 0 10px 0; color: #1e40af;">🔍 Filter Options Data</h3>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            `;
            
            // State data
            if (data.states) {
                html += `
                    <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px;">
                        <h4 style="margin: 0 0 15px 0; color: #374151;">🏛️ State List (${data.states.length})</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            ${data.states.map(state => 
                                `<span style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 12px; font-size: 12px;">${state}</span>`
                            ).join('')}
                        </div>
                    </div>
                `;
            }
            
            // Service type data
            if (data.services) {
                html += `
                    <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px;">
                        <h4 style="margin: 0 0 15px 0; color: #374151;">🏥 Service Types (${data.services.length})</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            ${data.services.map(service => 
                                `<span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 12px; font-size: 12px;">${service}</span>`
                            ).join('')}
                        </div>
                    </div>
                `;
            }
            
            html += `</div>`;
            
            // Coordinate data
            if (data.coordinates) {
                html += `
                    <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; margin-top: 20px;">
                        <h4 style="margin: 0 0 15px 0; color: #374151;">📍 State Coordinate Data</h4>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                                <thead style="background: #f9fafb; position: sticky; top: 0;">
                                    <tr>
                                        <th style="padding: 8px; text-align: left; border-bottom: 1px solid #e5e7eb;">State</th>
                                        <th style="padding: 8px; text-align: left; border-bottom: 1px solid #e5e7eb;">Latitude</th>
                                        <th style="padding: 8px; text-align: left; border-bottom: 1px solid #e5e7eb;">Longitude</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;
                
                Object.entries(data.coordinates).forEach(([state, coords], index) => {
                    html += `
                        <tr style="border-bottom: 1px solid #f3f4f6; ${index % 2 === 0 ? 'background: #fafafa;' : ''}">
                            <td style="padding: 8px; font-weight: 500;">${state}</td>
                            <td style="padding: 8px; font-family: monospace; color: #6b7280;">${coords.lat}</td>
                            <td style="padding: 8px; font-family: monospace; color: #6b7280;">${coords.lng}</td>
                        </tr>
                    `;
                });
                
                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }
            
            dataEl.innerHTML = html;
        }

        // Clear API data
        function clearApiData() {
            document.getElementById('api-data').innerHTML = '';
            document.getElementById('filter-data').innerHTML = '';
        }

        // Initialization when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🗺️ iframe example page loaded');
        });
    </script>
</body>
</html> 