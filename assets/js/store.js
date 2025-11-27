(function() {
  const CART_KEY = 'soncis-cart';
  const WISHLIST_KEY = 'soncis-wishlist';

  const parsePrice = (priceString) => {
    if (!priceString) return 0;
    return parseFloat(String(priceString).replace(/[₵$,]/g, '')) || 0;
  };

  const saveToStorage = (key, data) => {
    try {
      localStorage.setItem(key, JSON.stringify(data));
    } catch (error) {
      console.error('Storage error:', error);
    }
  };

  const getFromStorage = (key) => {
    try {
      const data = localStorage.getItem(key);
      return data ? JSON.parse(data) : [];
    } catch (error) {
      console.error('Storage error:', error);
      return [];
    }
  };

  const showToast = (message) => {
    if (window.Toastify) {
      Toastify({ text: message, duration: 3000, gravity: 'top', position: 'center', className: 'soncis-toast' }).showToast();
    } else {
      console.log(message);
    }
  };

  function addProductToCart(id, name, price, image) {
    const cart = getFromStorage(CART_KEY);
    const existing = cart.find((item) => item.id === id);

    if (existing) {
      existing.quantity += 1;
    } else {
      cart.push({ id, name, price, image, quantity: 1 });
    }

    saveToStorage(CART_KEY, cart);
    showToast('Added to cart');
    return cart;
  }

  function removeProductFromCart(id) {
    const cart = getFromStorage(CART_KEY);
    const itemId = typeof id === 'string' ? parseInt(id, 10) : Number(id);
    const filtered = cart.filter((item) => Number(item.id) !== itemId);
    saveToStorage(CART_KEY, filtered);
    return filtered;
  }

  function updateCartQuantity(id, quantity) {
    const cart = getFromStorage(CART_KEY);
    const itemId = typeof id === 'string' ? parseInt(id, 10) : Number(id);
    const item = cart.find((cartItem) => Number(cartItem.id) === itemId);
    if (item) {
      item.quantity = Math.max(1, quantity);
      saveToStorage(CART_KEY, cart);
    }
    return cart;
  }

  function addProductToWishlist(id, name, price, image, stock = 'In Stock') {
    const wishlist = getFromStorage(WISHLIST_KEY);
    const existing = wishlist.find((item) => item.id === id);

    if (!existing) {
      wishlist.push({ id, name, price, image, stock });
      saveToStorage(WISHLIST_KEY, wishlist);
      showToast('Added to wishlist');
    }
    return wishlist;
  }

  function removeProductFromWishlist(id) {
    const wishlist = getFromStorage(WISHLIST_KEY).filter((item) => item.id !== id);
    saveToStorage(WISHLIST_KEY, wishlist);
    return wishlist;
  }

  function isInWishlist(id) {
    const wishlist = getFromStorage(WISHLIST_KEY);
    return wishlist.some((item) => item.id === id);
  }

  window.SoncisStore = {
    addProductToCart,
    removeProductFromCart,
    updateCartQuantity,
    addProductToWishlist,
    removeProductFromWishlist,
    isInWishlist,
    parsePrice,
    getCart: () => getFromStorage(CART_KEY),
    getWishlist: () => getFromStorage(WISHLIST_KEY),
    clearCart: () => localStorage.removeItem(CART_KEY),
    clearWishlist: () => localStorage.removeItem(WISHLIST_KEY),
  };
})();
