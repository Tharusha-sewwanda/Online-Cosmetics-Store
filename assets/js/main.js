/* =========================================================
   Bellina Cosmetics — Vanilla JS (public site)
   Handles: live search/filter (AJAX), cart quantity updates,
   add-to-cart feedback, checkout form validation.
   ========================================================= */

document.addEventListener('DOMContentLoaded', function () {
    initFilterBar();
    initCartPage();
    initCheckoutForm();
});

/* ---------------- Search / Filter (AJAX) ---------------- */
function initFilterBar() {
    const form = document.getElementById('filterForm');
    if (!form) return;

    const grid = document.getElementById('productGrid');
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const skinTypeSelect = document.getElementById('skinTypeSelect');
    const clearBtn = document.getElementById('clearFiltersBtn');

    let debounceTimer;

    function runSearch() {
        const params = new URLSearchParams({
            search: searchInput.value.trim(),
            category: categorySelect.value,
            skin_type: skinTypeSelect.value,
        });

        grid.classList.add('loading');
        fetch('api/search.php?' + params.toString())
            .then((res) => res.json())
            .then((data) => {
                renderProducts(data.products);
            })
            .catch(() => {
                grid.innerHTML = '<p class="empty-state">Something went wrong while searching. Please try again.</p>';
            })
            .finally(() => grid.classList.remove('loading'));
    }

    function renderProducts(products) {
        if (!products || products.length === 0) {
            grid.innerHTML = '<p class="empty-state">No products match your search. Try a different keyword or filter.</p>';
            return;
        }
        grid.innerHTML = products.map(productCardHtml).join('');
    }

    function productCardHtml(p) {
        const outOfStock = Number(p.stock_quantity) <= 0;
        return `
        <div class="product-card">
            <a href="product.php?id=${p.id}">
                <img src="${escapeHtml(p.image_url)}" alt="${escapeHtml(p.name)}">
            </a>
            <div class="product-info">
                <span class="product-brand">${escapeHtml(p.brand)}</span>
                <a href="product.php?id=${p.id}"><span class="product-name">${escapeHtml(p.name)}</span></a>
                <span class="product-meta">${escapeHtml(p.category)} ${p.shade_variant ? '· ' + escapeHtml(p.shade_variant) : ''}</span>
                <span class="badge ${outOfStock ? 'out' : ''}">${outOfStock ? 'Out of stock' : 'Skin type: ' + escapeHtml(p.skin_type)}</span>
                <span class="product-price">${formatPrice(p.price)}</span>
                <a class="btn btn-block" href="product.php?id=${p.id}">View Product</a>
            </div>
        </div>`;
    }

    function formatPrice(price) {
        const n = Number(price).toFixed(2);
        return 'Rs. ' + n.replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.innerText = str == null ? '' : str;
        return div.innerHTML;
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(runSearch, 350);
    });
    categorySelect.addEventListener('change', runSearch);
    skinTypeSelect.addEventListener('change', runSearch);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        runSearch();
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            categorySelect.value = '';
            skinTypeSelect.value = '';
            runSearch();
        });
    }
}

/* ---------------- Cart page interactions ---------------- */
function initCartPage() {
    const cartTable = document.getElementById('cartTable');
    if (!cartTable) return;

    cartTable.addEventListener('change', function (e) {
        if (e.target.classList.contains('cart-qty-input')) {
            const productId = e.target.dataset.productId;
            const qty = parseInt(e.target.value, 10) || 0;
            updateCartItem(productId, qty);
        }
    });

    cartTable.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item-btn')) {
            const productId = e.target.dataset.productId;
            updateCartItem(productId, 0);
        }
    });

    function updateCartItem(productId, qty) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', qty);

        fetch('cart.php?action=update', { method: 'POST', body: formData })
            .then(() => window.location.reload());
    }
}

/* ---------------- Checkout form validation ---------------- */
function initCheckoutForm() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const name = form.querySelector('#customerName').value.trim();
        const email = form.querySelector('#email').value.trim();
        const phone = form.querySelector('#phone').value.trim();
        const address = form.querySelector('#address').value.trim();
        const errorBox = document.getElementById('checkoutError');

        let error = '';
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!name || !email || !phone || !address) {
            error = 'Please fill in all required fields.';
        } else if (!emailPattern.test(email)) {
            error = 'Please enter a valid email address.';
        } else if (phone.length < 7) {
            error = 'Please enter a valid phone number.';
        }

        if (error) {
            e.preventDefault();
            errorBox.textContent = error;
            errorBox.style.display = 'block';
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
}
