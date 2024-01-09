// script.js

// Sample product data (replace with your actual data)
const products = [
    { name: 'DETI Hoodie', price: 35.00 },
    { name: 'DETI Mug', price: 10.00 },
    { name: 'Erasmus T-Shirt', price: 20.00 },
    { name: 'DETI Backpack', price: 45.00 },
    { name: 'UA Keychain', price: 12.00 },
    { name: 'DETI Sweatpants', price: 25.00 },
    { name: 'UA Sunglasses', price: 15.00 },
    { name: 'UA Notebook Set', price: 8.00 }
];


// Function to display products in the product-listings container
function displayProducts() {
    let productList = document.getElementById('product-list');

    products.forEach(product => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <span class="product-name">${product.name}</span>
            <span class="product-price">$${product.price.toFixed(2)}</span>
        `;
        productList.appendChild(listItem);
    });
}

function displayCart() {
    const productList = document.getElementById('cart-list');

    // Filter the products array to include only items that are in the cart
    const productsInCart = products.filter(product => {
        return cart.items.some(item => item.name === product.name);
    });

    productsInCart.forEach(product => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <span class="product-name">${product.name}<const name = addToCartButton.dataset.name;/span>
            <span class="product-price">$${product.price.toFixed(2)}</span>
        `;
        productList.appendChild(listItem);
    });
}

// JavaScript for cart functionality
        const cart = {
            items: [],
            total: 0,
            addToCart: function (product, quantity) {
                if (isNaN(quantity)) {
                    alert("Error - requested amount is not a number");
                    return 1;
                }
                if (quantity < 0) return;
                const existingItem = this.items.find(item => item.name === product.name);
                if (existingItem) {
                    existingItem.quantity += quantity;
                    existingItem.totalPrice = existingItem.quantity * product.price;
                } else {
                    const newItem = {
                        name: product.name,
                        price: product.price,
                        quantity: quantity,
                        totalPrice: quantity * product.price,
                    };
                    this.items.push(newItem);
                }
                this.total = this.items.reduce((acc, item) => acc + item.totalPrice, 0);
                this.updateCartDisplay();
                this.saveCartToLocalStorage();
            },
            removeFromCart: function (product) {
                const res = this.items.find(item => item.name === product.name);
                this.total -= res.price * res.quantity;

                const index = this.items.findIndex(item => item.name === product.name);
                this.items.splice(index, 1);
                this.updateCartDisplay();
            },
            // NOT WORKING
            clearCart: function () {
                this.items = [];
                this.total = 0;
                this.updateCartDisplay();
            },
            updateCartDisplay: function () {
                const cartList = document.getElementById('cart-items');
                const cartTotal = document.getElementById('cart-total');
                cartList.innerHTML = '';
                this.items.forEach(item => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <span>${item.name} (Qty: ${item.quantity}) - $${item.totalPrice.toFixed(2)}</span>`;
                        cartList.appendChild(li);
                });
                cartTotal.textContent = this.total.toFixed(2);
                this.saveCartToLocalStorage();
            },
            checkout: function() {
                // CODE TO SIMULATE CHECKOUT FUNCTIONALITY

                // Used only for simulation of a purchase
                const attackerAccount = {boughtItems: [], accountTotal:30.00};

                if (attackerAccount.accountTotal < this.total) {
                    alert("Error in purchase - insufficient money in account\n");
                    return 1;
                } else {
                    // Add items to attacker account
                    for(p in this.items) {
                        attackerAccount.boughtItems.push(p);
                    }
                    attackerAccount.accountTotal -= this.total;
                    alert("Purchase completed\n");
                    return 0;
                }
            },
            saveCartToLocalStorage: function () {
                localStorage.setItem('cart', JSON.stringify(this));
            },
            loadCartFromLocalStorage: function () {
                const savedCart = localStorage.getItem('cart');
                if (savedCart) {
                    const parsedCart = JSON.parse(savedCart);
                    this.items = parsedCart.items;
                    this.total = parsedCart.total;
                    this.updateCartDisplay();
                }
            },
        };

        // Load the cart from local storage on page load
        //cart.loadCartFromLocalStorage();

        const productList = document.getElementById("cart-list");

        products.forEach(product => {
            const li = document.createElement('li');
            li.innerHTML = `
                <span class="product-name">${product.name}</span>
                <span class="product-price">$${product.price.toFixed(2)}</span>
                <input type="number" class="product-quantity" value="1">
                <button class="add-to-cart" data-name="${product.name}">Add to Cart</button>
                <button class="remove-from-cart" data-name="${product.name}">Remove</button>`
                ;
            productList.appendChild(li);

            const addToCartButton = li.querySelector('.add-to-cart');
            addToCartButton.addEventListener('click', () => {
                const name = addToCartButton.dataset.name;
                const price = products.find(p => p.name === name).price;
                const quantity = parseInt(li.querySelector('.product-quantity').value);
                cart.addToCart({ name, price }, quantity);
            });

            const removeFromCartButton = li.querySelector('.remove-from-cart');
            removeFromCartButton.addEventListener('click', () => {
                const name = addToCartButton.dataset.name;
                const prod = products.find(p => p.name === name);
                cart.removeFromCart(prod);
            });

        });
    
        const clearCartButton = document.getElementById("clear-cart");
        clearCartButton.addEventListener("click", cart.clearCart);

        const checkoutButton = document.getElementById("checkout");
        checkoutButton.addEventListener("click", cart.checkout);

        /*const cartList = document.getElementById('.cart-items');
        cartList.addEventListener('click', () => {
                const itemName = target.dataset.name;
                cart.removeFromCart(itemName);
            })*/
