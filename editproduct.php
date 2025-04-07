<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
// require_once 'check_admin.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Product</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Lora:wght@600;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <div class="container-fluid fixed-top px-0 wow fadeIn" data-wow-delay="0.1s">

        <nav class="navbar navbar-expand-lg navbar-light py-lg-0 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
            <a href="admin.php" class="navbar-brand ms-4 ms-lg-0">
                <h1 class="fw-bold m-0">Jem's Coffee Shop</h1>
            </a>
            <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto p-4 p-lg-0">
                    <a href="admin.php" class="nav-item nav-link ">Home</a>
<a href="editproduct.php" class="nav-item nav-link active" >Edit Product</a>

                    <a href="orders.html" class="nav-item nav-link">Orders</a>

                </div>
                <div class="d-none d-lg-flex ms-2">
                    <a class="btn-sm-square bg-white rounded-circle ms-3" href="">
                        <small class="fa fa-search text-body"></small>
                    </a>
                    <a class="btn-sm-square bg-white rounded-circle ms-3" href="">
                        <small class="fa fa-user text-body"></small>
                    </a>
                    <a class="btn-sm-square bg-white rounded-circle ms-3" href="wala.html">
    <small class="fa fa-sign-out-alt text-body"></small>
</a>
                </div>
            </div>
        </nav>
    </div>
    <div class="container-xxl py-6">
        <div class="container">
            <div class="row g-0 gx-5 align-items-end">
                <div class="col-lg-6">
                    <div class="section-header text-start mb-5 wow fadeInUp" data-wow-delay="0.1s"
                        style="max-width: 500px;">
                        <h1 class="display-5 mb-3">Our Products</h1>
                        <p>Indulge in the perfect blend of flavor and aroma with our premium coffee selection.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus"></i> Add Product
                        </button>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                
                <div id="tab-1" class="tab-pane fade show p-0 active">
                    <div class="row g-4" id="productsContainer">
                        <!-- Products will be loaded dynamically here -->
                    </div>
                </div>
                <div id="tab-2" class="tab-pane fade show p-0">
                    <div class="row g-4" id="productsContainer2">
                        <!-- Products will be loaded dynamically here -->
                    </div>
                </div>
                <div id="tab-3" class="tab-pane fade show p-0">
                    <div class="row g-4" id="productsContainer3">
                        <!-- Products will be loaded dynamically here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i
            class="bi bi-arrow-up"></i></a>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <!-- Remove duplicate Bootstrap JS -->
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Load products immediately
            loadProducts();
        
            // Image preview functionality
            const imageFileInput = document.getElementById('productImageFile');
            const imageUrlInput = document.getElementById('productImageUrl');
            const imagePreview = document.getElementById('imagePreview');
        
            if (imageFileInput && imagePreview) {
                imageFileInput.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.classList.remove('d-none');
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
        
            if (imageUrlInput && imagePreview) {
                imageUrlInput.addEventListener('input', function(e) {
                    if (this.value) {
                        imagePreview.src = this.value;
                        imagePreview.classList.remove('d-none');
                    } else {
                        imagePreview.src = '';
                        imagePreview.classList.add('d-none');
                    }
                });
            }
        
            // Add product form submission
            const addProductForm = document.getElementById("addProductForm");
            if (addProductForm) {
                addProductForm.addEventListener("submit", async function(e) {
                    e.preventDefault();
                    
                    const name = document.getElementById("newProductName").value;
                    const price = document.getElementById("newProductPrice").value;
                    const imageUrl = document.getElementById("productImageUrl").value;
                    const imageFile = document.getElementById("productImageFile").files[0];
                    
                    console.log("Submitting product:", {name, price, imageUrl, imageFile});
                    
                    if (!name || !price) {
                        alert("Please fill all required fields");
                        return;
                    }
                    
                    if (!imageUrl && !imageFile) {
                        alert("Please provide either an image URL or upload a file");
                        return;
                    }
        
                    try {
                        let image_path = '';
                        let image_url = imageUrl;
                        
                        // Handle file upload
                        if (imageFile) {
                            console.log("Uploading file...");
                            const formData = new FormData();
                            formData.append('image', imageFile);
                            
                            const uploadResponse = await fetch('api/upload.php', {
                                method: 'POST',
                                body: formData
                            });
                            
                            const uploadResult = await uploadResponse.json();
                            console.log("Upload result:", uploadResult);
                            
                            if (!uploadResult.success) {
                                throw new Error(uploadResult.message);
                            }
                            
                            image_path = uploadResult.path;
                            image_url = ''; // Clear URL if file was uploaded
                        }
        
                        // Submit product data
                        const response = await fetch('api/add_product.php', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                name: name,
                                price: parseFloat(price),
                                image_url: image_url,
                                image_path: image_path
                            })
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const result = await response.json();
                        console.log("Add product result:", result);
                        
                        if (result.success) {
                            alert("Product added successfully!");
                            $('#addModal').modal('hide');
                            addProductForm.reset();
                            if (imagePreview) {
                                imagePreview.src = '';
                                imagePreview.classList.add('d-none');
                            }
                            loadProducts(); // Refresh the product list
                        } else {
                            throw new Error(result.message || "Failed to add product");
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert("Error: " + error.message);
                    }
                });
            }
        
            // Load products function
            async function loadProducts() {
    try {
        console.log("Loading products...");
        const response = await fetch('api/get_products.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const products = await response.json();
        console.log("Products received:", products);
        
        // Get all product containers
        const containers = [
            document.getElementById('productsContainer'),
            document.getElementById('productsContainer2'), 
            document.getElementById('productsContainer3')
        ].filter(Boolean);
        
        if (containers.length === 0) {
            console.error('No product containers found');
            return;
        }
        
        // Clear all containers
        containers.forEach(container => {
            container.innerHTML = '';
        });
        
        // Add products to each container
        products.forEach(product => {
            // Convert price to number if it's a string
            const price = typeof product.price === 'string' 
                ? parseFloat(product.price) 
                : product.price;
            
            const imageSrc = product.image_path || product.image_url || 'img/default-coffee.jpg';
            const productHTML = `
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="product-item">
                        <div class="position-relative bg-light overflow-hidden">
                            <img class="img-fluid w-100" src="${imageSrc}" 
                                 alt="${product.name}" 
                                 onerror="this.src='img/default-coffee.jpg'">
                            <div class="bg-secondary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">New</div>
                        </div>
                        <div class="text-center p-4">
                            <a class="d-block h5 mb-2" href="">${product.name}</a>
                            <span class="text-primary me-1">$${price.toFixed(2)}</span>
                            <button class="btn btn-sm btn-warning edit-btn me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal"
                                data-id="${product.id}"
                                data-name="${product.name}"
                                data-price="${price}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" 
                                data-id="${product.id}"
                                data-name="${product.name}">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add to each container
            containers.forEach(container => {
                container.innerHTML += productHTML;
            });
        });
    } catch (error) {
        console.error('Error loading products:', error);
    }
}




            // Edit product functionality
            // Edit product functionality
            // Delete product functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-btn')) {
                    const productId = e.target.getAttribute('data-id');
                    const productName = e.target.getAttribute('data-name');
                    
                    if (confirm(`Are you sure you want to delete "${productName}"?`)) {
                        fetch('api/delete_product.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ id: productId })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                alert(`"${productName}" deleted successfully!`);
                                loadProducts();
                            } else {
                                throw new Error(result.message || "Failed to delete product");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert("Error: " + error.message);
                        });
                    }
                }
                
                // Edit product functionality with improved modal handling
                if (e.target.classList.contains('edit-btn')) {
                const productId = e.target.getAttribute('data-id');
                const productName = e.target.getAttribute('data-name');
                const productPrice = e.target.getAttribute('data-price');
                
                document.getElementById('productName').value = productName;
                document.getElementById('productPrice').value = productPrice;
                document.getElementById('originalProductName').value = productName;
                
                // Clear previous event listeners
                const saveBtn = document.getElementById('saveChanges');
                saveBtn.replaceWith(saveBtn.cloneNode(true));
                
                // Set up new save button handler
                document.getElementById('saveChanges').onclick = async function() {
                    const newName = document.getElementById('productName').value;
                    const newPrice = parseFloat(document.getElementById('productPrice').value);
                    
                    if (!newName || isNaN(newPrice)) {
                        alert("Please fill all fields with valid values");
                        return;
                    }
                    
                    try {
                        const response = await fetch('api/edit_product.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                id: productId,
                                name: newName,
                                price: newPrice
                            })
                        });
                        
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            const text = await response.text();
                            throw new Error(`Invalid response: ${text}`);
                        }
                        
                        const result = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(result.message || "Failed to update product");
                        }
                        
                        if (result.success) {
                            alert("Product updated successfully!");
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                            modal.hide();
                            document.body.classList.remove('modal-open');
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) backdrop.remove();
                            loadProducts();
                        } else {
                            throw new Error(result.message || "Failed to update product");
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert("Error: " + error.message);
                    }
                };
            }
        });
        });
        </script>
    <!-- Add Product Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm">
                    <div class="mb-3">
                        <label for="newProductName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="newProductName" required>
                    </div>
                    <div class="mb-3">
                        <label for="newProductPrice" class="form-label">Price ($)</label>
                        <input type="number" step="0.01" class="form-control" id="newProductPrice" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <div class="d-flex flex-column gap-2">
                            <!-- URL Input -->
                            <div>
                                <label class="form-label small">Image URL</label>
                                <input type="text" class="form-control" id="productImageUrl" 
                                       placeholder="https://example.com/image.jpg">
                            </div>
                            <!-- OR divider -->
                            <div class="text-center fw-bold">OR</div>
                            <!-- File Upload -->
                            <div>
                                <label class="form-label small">Upload Image</label>
                                <input type="file" class="form-control" id="productImageFile" 
                                       accept="image/jpeg, image/png, image/webp">
                                <div class="form-text">Max 2MB (JPEG, PNG, WEBP)</div>
                            </div>
                        </div>
                        <!-- Image Preview -->
                        <div class="mt-3 text-center">
                            <img id="imagePreview" src="" class="img-thumbnail d-none" 
                                 style="max-height: 150px;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName">
                    </div>
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Price ($)</label>
                        <input type="number" step="0.01" class="form-control" id="productPrice">
                    </div>
                    <input type="hidden" id="originalProductName">
                    <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>