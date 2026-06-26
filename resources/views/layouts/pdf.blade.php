<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/color-template.css', 'resources/css/app.css'])
    <style>
        @page {
            size: A4;
            margin: 1cm 0.25cm;
        }

        body {
            -webkit-print-color-adjust: exact;
            background-color: white !important;
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
    </style>

</head>

<body class="bg-white scrollbar-x-large">
    <div class="p-8">
        @yield('content')
    </div>
</body>

</html>
