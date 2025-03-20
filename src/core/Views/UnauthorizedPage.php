<?php
namespace jdTonido\RBAC\core\Views;
class UnauthorizedPage{
    /**
     * Generate the unauthorized access page.
     *
     * @param string $message Custom error message (optional)
     * @return string The HTML content
     */
    public static function render($message = "You do not have permission to access this page.")
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 | Forbidden</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    background-color: #f8fafc;
                    color: #333;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    flex-direction: column;
                }
                h1 {
                    font-size: 72px;
                    margin: 0;
                    color: #d9534f;
                }
                p {
                    font-size: 20px;
                    margin-top: 10px;
                }
                a {
                    display: inline-block;
                    margin-top: 20px;
                    text-decoration: none;
                    color: #fff;
                    background-color: #007bff;
                    padding: 10px 20px;
                    border-radius: 5px;
                }
                a:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>

        <div class="container">
            <h1>403</h1>
            <p>{$message}</p>
            <a href="/">Go Back Home</a>
        </div>

        </body>
        </html>
        HTML;
    }
}