<?php
namespace jdTonido\RBAC\core\Views;
class UnauthorizedPage{
    /**
     * Generate the unauthorized access page.
     *
     * @param string $message Custom error message (optional)
     * @return string The HTML content
     */
    public static function render($message = "You do not have permission to access this page.", $url = '/')
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 | Forbidden</title>
        </head>
        <body style="font-family: Arial, sans-serif; text-align: center; background-color: #f8fafc; color: #333; margin: 0; padding: 0;">

            <div style="display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column;">
                <h1 style="font-size: 72px; margin: 0; color: #d9534f;">403</h1>
                <p style="font-size: 20px; margin-top: 10px;">{$message}</p>
                <a href="{$url}" style="display: inline-block; margin-top: 20px; text-decoration: none; color: #fff; background-color: #007bff; padding: 10px 20px; border-radius: 5px;">Go Back Home</a>
            </div>

        </body>
        </html>
        HTML;
    }
}