<script>
    document.addEventListener("livewire:navigated", function() {
        const cartButtons = document.querySelectorAll(".add-to-cart");

        cartButtons.forEach(cartButton => {

            cartButton.addEventListener("click", function() {
                const productId = cartButton.getAttribute("data-product");
                Livewire.dispatch("addToCart", productId);
            });

        });

    });
</script>