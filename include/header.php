<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Simple Invoice</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- jsPDF and AutoTable Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- script.js -->
    <script src="assets/preview.js"></script>
    <script src="assets/generate-invoice.js"></script>
</head>

<body>
    <header>

        <nav class="bg-light">
            <div class="max-w-screen-xl mx-auto px-4 py-2 flex items-center justify-between">
                <!-- Logo -->
                <a class="flex items-center" href="#">
                    <img src="https://cdn3.geckoandfly.com/wp-content/uploads/2019/06/530-invoice-templates.jpg" alt="Logo" class="h-10">
                </a>

                <!-- Navbar Links -->
                <div class="hidden md:flex space-x-8">
                    <a href="../index.php" class="text-gray-800 hover:text-blue-500 no-underline">Home</a>
                    <a href="../invoice-demo/DB_Files/Stock.php"
                        class="text-gray-800 hover:text-blue-500 no-underline">Stock-Management</a>
                    <a href="/phpcode/project%20management/invoice-demo/front-end_files/invoice-list.php"
                        class="text-gray-800 hover:text-blue-500 no-underline">Invoice-List</a>

                    <a href="#" class="text-gray-800 hover:text-blue-500 no-underline">Contact</a>
                    <a href="../INVOICE-DEMO/sales_report/sales_report.php" class="text-gray-800 hover:text-blue-500 no-underline">Sales Report</a>
                    <a href="/phpcode/project management/INVOICE-DEMO/Login/logout.php" class="text-gray-800 hover:text-blue-500 no-underline">Logout</a>

                </div>


                <!-- Mobile Toggle Button -->
                <div class="md:hidden flex items-center">
                    <button class="text-gray-800 hover:text-blue-500 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu (hidden by default, will show on mobile) -->
            <div class="md:hidden bg-light px-4 py-2">
                <ul class="space-y-4">
                    <li><a href="../index.php" class="text-gray-800 hover:text-blue-500">Home</a></li>
                    <li><a href="../DB_Files/Stock.php" class="text-gray-800 hover:text-blue-500">Stock Management</a>
                    </li>
                    <li><a href="/phpcode/project%20management/invoice-demo/front-end_files/invoice-list.php"
                            class="text-gray-800 hover:text-blue-500 no-underline">Invoice-List</a>
                    </li>
                    <li><a href="#" class="text-gray-800 hover:text-blue-500">Contact</a></li>
                </ul>
            </div>
        </nav>


    </header>


    <!-- Sidebar Menu (Offcanvas) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-unstyled">
                <li><a href="http://localhost/phpcode/project%20management/invoice-demo/../index.php"
                        class="btn btn-link">Home</a></li>
                <li><a href="http://localhost/phpcode/project%20management/invoice-demo/../DB_Files/Stock.php"
                        class="btn btn-link">Stock Management</a></li>
                <li><a href="/phpcode/project%20management/invoice-demo/front-end_files/invoice-list.php"
                        class="text-gray-800 hover:text-blue-500 no-underline">Invoice-List</a>
                </li>
                <li><a href="http://localhost/phpcode/project%20management/invoice-demo/Contact.php"
                        class="btn btn-link">Contact</a></li>
            </ul>
        </div>
    </div>
    </header>
    <!-- End Header -->