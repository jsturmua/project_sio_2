$(document).ready(function(){
    loadProducts();
    $('#search').on('input', function() {loadProducts();});
    $('#filter').click(function() {loadProducts();});

    $(document).on('click', '.product', function() {
        var productId = $(this).data('product-id');
        if (productId) {window.location.href = 'product_details.html?id=' + productId;}
    });
 
    function loadProducts() {
        var search = $('#search').val();
        var category = $('#category').val();

        $.ajax({
            url: '../php/products.php',method: 'POST',
            data: { search: search, category: category },
            success: function(response) {$('#product-list').html(response);}
        });
    }
});