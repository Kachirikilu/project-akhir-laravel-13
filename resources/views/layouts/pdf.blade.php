<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/color-template.css', 'resources/css/app.css'])

    <style>
        @page {
            size: A4;
        }

        body {
            -webkit-print-color-adjust: exact;
            background-color: white !important;
            margin: 0;
            padding: 0;
        }

        table {
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }

        .page-break {
            page-break-before: always;
        }
        .page-container {
            page-break-after: always;
            break-inside: avoid;
        }
    </style>

</head>

<body class="bg-white scrollbar-x-large">
    @yield('content')
</body>

</html>
