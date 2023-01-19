const language = $('html')[0].lang;
IndexControllerURL = "/index_controller";
ProductControllerURL = "/show_product";
CartControllerURL = "/cart";

var products = [];

const sortList = [
    { label: 'smallestPrice', field: 'price', orderBy: 'ASC' },
    { label: 'biggestPrice', field: 'price', orderBy: 'DESC' },
    { label: 'newest', field: 'start_date', orderBy: 'DESC' }
];

function getProducts() {
    $.ajax({
        type: 'POST',
        url: IndexControllerURL,
        dataType: 'text',
        data: {
            getProducts: true,
        },
        success: function (data) {
            products = JSON.parse(data);
            console.log(products);
            products = JSON.parse(products);
            console.log(products);
        }
    });

    return products;
}

function handleChange(sortType) {
    currentId = sortType.id;

    this.IndexControllerURL = document.getElementById(currentId).value;
    console.log(this.IndexControllerURL);
    sortList.forEach(option => {
        if (currentId == option.label) {
            document.getElementById(option.label).checked = true;

            $.ajax({
                type: 'POST',
                url: IndexControllerURL,
                dataType: 'text',
                data: {
                    field: option.field,
                    orderBy: option.orderBy
                },
                success: function (data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    console.log(obj);
                    console.log(obj.result);
                }
            });
        } else {
            document.getElementById(option.label).checked = false;
        }
    })
}

function addValue(id) {
    let amountOfProducts = document.getElementById('amountOfProducts').value;
    let currentValue = document.getElementById('productAmount').value;
    if (id === 'plus' && currentValue < parseInt(amountOfProducts)) {
        document.getElementById('productAmount').value = parseInt(currentValue) + 1;
    } else if (id === 'minus' && currentValue > 1) {
        document.getElementById('productAmount').value = parseInt(currentValue) - 1;
    }
}

function addAmount() {
    let amountOfProducts = document.getElementById('amountOfProducts').value;
    let currentValue = document.getElementById('productAmount').value;
    if (Number.isInteger(parseInt(currentValue))) {
        if (parseInt(currentValue) <= parseInt(amountOfProducts)) {
            if (parseInt(currentValue) < 1) {
                document.getElementById('productAmount').value = 1;
                if ('pl' === language) {
                    document.getElementById("message").innerHTML = 'Wprowadzona ilość nie może być mniejsza od 1.';
                } else {
                    document.getElementById("message").innerHTML = 'The desired amount cannot be less than 1.';
                }
            }
        } else {
            document.getElementById('productAmount').value = parseInt(amountOfProducts);
            if ('pl' === language) {
                document.getElementById("message").innerHTML = 'Wprowadzona ilość nie może być większa od ilości w magazynie.';
            } else {
                document.getElementById("message").innerHTML = 'The quantity entered cannot be greater than the quantity in storage.';
            }
        }
    } else {
        document.getElementById('productAmount').value = 1;
        if ('pl' === language) {
            document.getElementById("message").innerHTML = 'Wprowadzona ilość musi być liczbą całkowitą.';
        } else {
            document.getElementById("message").innerHTML = 'The quantity entered must be a whole number.';
        }
    }
}


function addToCart() {
    let productId = document.getElementById('productId').value;
    let productAmount = document.getElementById('productAmount').value;

    $.ajax({
        type: 'POST',
        url: ProductControllerURL,
        dataType: 'text',
        data: {
            productId: productId,
            productAmount: productAmount,
        },
        success: function (data) {
            if (JSON.parse(data) === "login") {
                if ('pl' === language) {
                    document.getElementById("message").innerHTML = 'Musisz się najpierw zalogować aby dodać przedmiot do koszyka.';
                } else {
                    document.getElementById("message").innerHTML = 'You must first sign in to add an item to your cart.';
                }
            } else if (JSON.parse(data) === "addedToCart") {

                document.location.reload(ProductControllerURL)
                if ('pl' === language) {
                    document.getElementById("message").innerHTML = 'Produkt został dodany do koszyka!';
                } else {
                    document.getElementById("message").innerHTML = 'Product has been added to your cart!';
                }
            }
        }
    });
}

function deleteProductFromCart(productId) {
    console.log('JESTEM?');
    console.log('productId: ' + JSON.stringify(productId));
    $.ajax({
        type: 'POST',
        url: CartControllerURL,
        dataType: 'text',
        data: {
            removeCartItemProductId: productId.id,
        },
        success: function (data) {
            document.location.reload(CartControllerURL)
        }
    });
}


