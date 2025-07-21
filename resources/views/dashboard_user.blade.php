<x-user-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-black">Pencarian Kategori Produk</h2>
                        <form id="searchFormKategori" class="flex items-center gap-1" onsubmit="return false;">
                            <input type="text" id="searchInputKategori" name="search" placeholder="Cari kategori..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button id="searchButtonKategori" type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-sm transition duration-150">
                                Cari
                            </button>
                        </form>
                    </div>
                    <div id="paginationKategori" class="mt-4 flex justify-center space-x-2"></div>
                    <div id="resultsSectionKategori" class="mt-6">
                        <!-- Search results table will be inserted here -->
                    </div>
                </div>                
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchFormKategori').addEventListener('submit', function(event) {
            event.preventDefault();
            const query = document.getElementById('searchInputKategori').value.trim();
            if (!query) {
                alert('Masukkan kategori produk untuk mencari.');
                return;
            }
            const apiUrl = `/api/v1/produk/category?q=${encodeURIComponent(query)}`;

        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                renderResultsKategori(data.data);
                renderPaginationKategori(data);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Terjadi kesalahan saat mengambil data produk.');
            });
        });
        

        function renderResultsKategori(products) {
            const resultsSection = document.getElementById('resultsSectionKategori');
            if (!products || products.length === 0) {
                resultsSection.innerHTML = '<p class="text-red-600">Tidak ada kategori produk ditemukan.</p>';
                return;
            }

            let tableHtml = `
                <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">Nama Produk</th>
                            <th class="border px-4 py-2 text-left">Kategori</th>
                            <th class="border px-4 py-2 text-left">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            products.forEach(product => {
                tableHtml += `
                    <tr>
                        <td class="border px-4 py-2">${product.nama_produk}</td>
                        <td class="border px-4 py-2">${product.nama_kategori}</td>
                        <td class="border px-4 py-2">Rp ${Number(product.harga_produk).toLocaleString('id-ID')}</td>
                    </tr>
                `;
            });

            tableHtml += `
                    </tbody>
                </table>
            `;

            resultsSection.innerHTML = tableHtml;
        }
        function renderPaginationKategori(data) {
            const paginationContainer = document.getElementById('paginationKategori');
            paginationContainer.innerHTML = '';

            if (data.last_page <= 1) {
                return; // No pagination needed
            }

            const nav = document.createElement('nav');
            nav.setAttribute('aria-label', 'Pagination Navigation');
            nav.classList.add('inline-flex', 'items-center', 'space-x-1', 'rounded-md', 'shadow-sm');

            const ul = document.createElement('ul');
            ul.classList.add('inline-flex', 'items-center', 'space-x-1');

            const createPageButton = (page, text = null, disabled = false, ariaLabel = null) => {
                const li = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = text || page;
                button.classList.add(
                    'px-3', 'py-1', 'border', 'rounded-md', 'focus:outline-none', 'focus:ring-2', 'focus:ring-offset-1', 'focus:ring-blue-500',
                    'transition', 'duration-150', 'ease-in-out'
                );

                if (disabled) {
                    button.classList.add('bg-gray-200', 'text-gray-500', 'cursor-not-allowed');
                    button.disabled = true;
                } else if (page === data.current_page) {
                    button.classList.add('bg-blue-600', 'text-white', 'cursor-default');
                    button.disabled = true;
                } else {
                    button.classList.add('bg-white', 'text-gray-700', 'hover:bg-gray-100');
                    button.addEventListener('click', () => {
                        fetchCategoryPage(page);
                    });
                }

                if (ariaLabel) {
                    button.setAttribute('aria-label', ariaLabel);
                } else {
                    button.setAttribute('aria-label', `Page ${page}`);
                }

                li.appendChild(button);
                return li;
            };

            
            const prevDisabled = data.current_page <= 1;
            ul.appendChild(createPageButton(data.current_page - 1, 'Previous', prevDisabled, 'Previous Page'));

            
            const totalPages = data.last_page;
            const currentPage = data.current_page;
            const maxPagesToShow = 7;
            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = startPage + maxPagesToShow - 1;

            if (endPage > totalPages) {
                endPage = totalPages;
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            if (startPage > 1) {
                ul.appendChild(createPageButton(1));
                if (startPage > 2) {
                    const ellipsis = document.createElement('li');
                    ellipsis.innerHTML = '<span class="px-3 py-1 text-gray-500 select-none">...</span>';
                    ul.appendChild(ellipsis);
                }
            }

            for (let page = startPage; page <= endPage; page++) {
                ul.appendChild(createPageButton(page));
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('li');
                    ellipsis.innerHTML = '<span class="px-3 py-1 text-gray-500 select-none">...</span>';
                    ul.appendChild(ellipsis);
                }
                ul.appendChild(createPageButton(totalPages));
            }

            // Next button
            const nextDisabled = data.current_page >= data.last_page;
            ul.appendChild(createPageButton(data.current_page + 1, 'Next', nextDisabled, 'Next Page'));

            nav.appendChild(ul);
            paginationContainer.appendChild(nav);
        }

        function fetchCategoryPage(page) {
            const query = document.getElementById('searchInputKategori').value.trim();
            if (!query) {
                alert('Masukkan kategori produk untuk mencari.');
                return;
            }
            const apiUrl = `/api/v1/produk/category?q=${encodeURIComponent(query)}&page=${page}`;

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    renderResultsKategori(data.data);
                    renderPaginationKategori(data);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Terjadi kesalahan saat mengambil data produk.');
                });
        }
    </script>

            
    <div class="py-12">    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-black">Pencarian Produk</h2>
                        <form id="searchFormProduk" class="flex items-center gap-1" onsubmit="return false;">
                            <input type="text" id="searchInputProduk" name="search" placeholder="Cari produk..." class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button id="searchButtonProduk" type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-sm transition duration-150">
                                Cari
                            </button>
                        </form>
                    </div>
                    
                    <div id="resultsSectionProduk" class="mt-6">
                        <!-- Search results table will be inserted here -->
                    </div>
                </div>                
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchFormProduk').addEventListener('submit', function(event) {
            event.preventDefault();
            const query = document.getElementById('searchInputProduk').value.trim();
            if (!query) {
                alert('Masukkan nama produk untuk mencari.');
                return;
            }
            const apiUrl = `/api/v1/produk/search?q=${encodeURIComponent(query)}`;

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    renderResultsProduk(data);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Terjadi kesalahan saat mengambil data produk.');
                });
        });

        function renderResultsProduk(products) {
            const resultsSection = document.getElementById('resultsSectionProduk');
            if (!products || products.length === 0) {
                resultsSection.innerHTML = '<p class="text-red-600">Tidak ada produk ditemukan.</p>';
                return;
            }

            let tableHtml = `
                <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">Nama Produk</th>
                            <th class="border px-4 py-2 text-left">Kategori</th>
                            <th class="border px-4 py-2 text-left">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            products.forEach(product => {
                tableHtml += `
                    <tr>
                        <td class="border px-4 py-2">${product.nama_produk}</td>
                        <td class="border px-4 py-2">${product.nama_kategori}</td>
                        <td class="border px-4 py-2">Rp ${Number(product.harga_produk).toLocaleString('id-ID')}</td>
                    </tr>
                `;
            });

            tableHtml += `
                    </tbody>
                </table>
            `;

            resultsSection.innerHTML = tableHtml;
        }

        
    </script>

    <!-- New section for price range search -->
    <div class="py-12">    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-black">Pencarian Rentang Harga Produk</h2>
                        <form id="searchFormRentangHarga" class="flex items-center gap-2" onsubmit="return false;">
                            <input type="number" id="minPrice" name="minPrice" placeholder="Harga minimum" min="0" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <input type="number" id="maxPrice" name="maxPrice" placeholder="Harga maksimum" min="0" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <button id="searchButtonRentangHarga" type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-sm transition duration-150">
                                Cari
                            </button>
                        </form>
                    </div>

                    <div id="resultsSectionRentangHarga" class="mt-6">
                        <!-- Search results table will be inserted here -->
                    </div>
                </div>                
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchFormRentangHarga').addEventListener('submit', function(event) {
            event.preventDefault();
            const minPrice = document.getElementById('minPrice').value.trim();
            const maxPrice = document.getElementById('maxPrice').value.trim();

            if (minPrice === '' && maxPrice === '') {
                alert('Masukkan minimal satu nilai harga (minimum atau maksimum).');
                return;
            }

            if (minPrice !== '' && isNaN(minPrice)) {
                alert('Harga minimum harus berupa angka.');
                return;
            }

            if (maxPrice !== '' && isNaN(maxPrice)) {
                alert('Harga maksimum harus berupa angka.');
                return;
            }

            const params = new URLSearchParams();
            if (minPrice !== '') params.append('min_price', minPrice);
            if (maxPrice !== '') params.append('max_price', maxPrice);

            const apiUrl = `/api/v1/produk/rentangharga?${params.toString()}`;

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    renderResultsRentangHarga(data);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Terjadi kesalahan saat mengambil data produk.');
                });
        });

        function renderResultsRentangHarga(data) {
            const resultsSection = document.getElementById('resultsSectionRentangHarga');
            if (!data || !data.data || data.data.length === 0) {
                resultsSection.innerHTML = '<p class="text-red-600">Tidak ada produk ditemukan dalam rentang harga tersebut.</p>';
                document.getElementById('paginationRentangHarga').innerHTML = '';
                return;
            }

            let tableHtml = `
                <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">Nama Produk</th>
                            <th class="border px-4 py-2 text-left">Kategori</th>
                            <th class="border px-4 py-2 text-left">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.data.forEach(product => {
                tableHtml += `
                    <tr>
                        <td class="border px-4 py-2">${product.nama_produk}</td>
                        <td class="border px-4 py-2">${product.nama_kategori}</td>
                        <td class="border px-4 py-2">Rp ${Number(product.harga_produk).toLocaleString('id-ID')}</td>
                    </tr>
                `;
            });

            tableHtml += `
                    </tbody>
                </table>
            `;

            resultsSection.innerHTML = tableHtml;
            renderPaginationRentangHarga(data);
        }

        function renderPaginationRentangHarga(data) {
            const paginationContainer = document.getElementById('paginationRentangHarga');
            paginationContainer.innerHTML = '';

            if (data.last_page <= 1) {
                return; // No pagination needed
            }

            const nav = document.createElement('nav');
            nav.setAttribute('aria-label', 'Pagination Navigation');
            nav.classList.add('inline-flex', 'items-center', 'space-x-1', 'rounded-md', 'shadow-sm');

            const ul = document.createElement('ul');
            ul.classList.add('inline-flex', 'items-center', 'space-x-1');

            const createPageButton = (page, text = null, disabled = false, ariaLabel = null) => {
                const li = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = text || page;
                button.classList.add(
                    'px-3', 'py-1', 'border', 'rounded-md', 'focus:outline-none', 'focus:ring-2', 'focus:ring-offset-1', 'focus:ring-blue-500',
                    'transition', 'duration-150', 'ease-in-out'
                );

                if (disabled) {
                    button.classList.add('bg-gray-200', 'text-gray-500', 'cursor-not-allowed');
                    button.disabled = true;
                } else if (page === data.current_page) {
                    button.classList.add('bg-blue-600', 'text-white', 'cursor-default');
                    button.disabled = true;
                } else {
                    button.classList.add('bg-white', 'text-gray-700', 'hover:bg-gray-100');
                    button.addEventListener('click', () => {
                        fetchRentangHargaPage(page);
                    });
                }

                if (ariaLabel) {
                    button.setAttribute('aria-label', ariaLabel);
                } else {
                    button.setAttribute('aria-label', `Page ${page}`);
                }

                li.appendChild(button);
                return li;
            };

            const prevDisabled = data.current_page <= 1;
            ul.appendChild(createPageButton(data.current_page - 1, 'Previous', prevDisabled, 'Previous Page'));

            const totalPages = data.last_page;
            const currentPage = data.current_page;
            const maxPagesToShow = 7;
            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = startPage + maxPagesToShow - 1;

            if (endPage > totalPages) {
                endPage = totalPages;
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            if (startPage > 1) {
                ul.appendChild(createPageButton(1));
                if (startPage > 2) {
                    const ellipsis = document.createElement('li');
                    ellipsis.innerHTML = '<span class="px-3 py-1 text-gray-500 select-none">...</span>';
                    ul.appendChild(ellipsis);
                }
            }

            for (let page = startPage; page <= endPage; page++) {
                ul.appendChild(createPageButton(page));
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('li');
                    ellipsis.innerHTML = '<span class="px-3 py-1 text-gray-500 select-none">...</span>';
                    ul.appendChild(ellipsis);
                }
                ul.appendChild(createPageButton(totalPages));
            }

            const nextDisabled = data.current_page >= data.last_page;
            ul.appendChild(createPageButton(data.current_page + 1, 'Next', nextDisabled, 'Next Page'));

            nav.appendChild(ul);
            paginationContainer.appendChild(nav);
        }

        function fetchRentangHargaPage(page) {
            const minPrice = document.getElementById('minPrice').value.trim();
            const maxPrice = document.getElementById('maxPrice').value.trim();

            if (minPrice === '' && maxPrice === '') {
                alert('Masukkan minimal satu nilai harga (minimum atau maksimum).');
                return;
            }

            if (minPrice !== '' && isNaN(minPrice)) {
                alert('Harga minimum harus berupa angka.');
                return;
            }

            if (maxPrice !== '' && isNaN(maxPrice)) {
                alert('Harga maksimum harus berupa angka.');
                return;
            }

            const params = new URLSearchParams();
            if (minPrice !== '') params.append('min_price', minPrice);
            if (maxPrice !== '') params.append('max_price', maxPrice);
            params.append('page', page);

            const apiUrl = `/api/v1/produk/rentangharga?${params.toString()}`;

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    renderResultsRentangHarga(data);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Terjadi kesalahan saat mengambil data produk.');
                });
        }
    </script>
</x-user-layout>
