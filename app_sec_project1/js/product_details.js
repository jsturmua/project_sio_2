$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);const productId = urlParams.get('id');
 
    if (productId) {$.ajax({url: '../php/product_details.php',method: 'POST',data: { id: productId },success: function(response) {$('#product-details').html(response);}});}
    
    $("#comment-form").submit(function (event) {
        event.preventDefault();
        
        var username = $("#username").val();
        var comment = $("#comment").val();

        comment = $('<div />').text(comment).html();

        var commentElement = $("<div class='comment'><strong>" + username + ":</strong> " + comment + "</div>");

        $("#comment-list").append(commentElement);
        $("#username").val("");$("#comment").val("");
    });

    $(document).on('click', '.add-to-cart', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');

        $.ajax({url: '../php/add_to_cart.php', method: 'POST', data: { id: productId },success: function (response) {if (response === 'success') {alert('Success');} else {alert('Error');}}});
    });
});
