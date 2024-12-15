<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Under Maintenance</title>
    <style>
        /* Global Styling */
        :root {
            --primary-color: #dc3545;
            --secondary-color: #6c757d;
            --background-color: #f4f4f4;
            --container-background: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --font-family: 'Arial', sans-serif;
            --heading-font-size: 2.5rem;
            --subheading-font-size: 1.5rem;
            --text-font-size: 1.2rem;
            --button-background-color: #28a745;
            --button-hover-color: #218838;
            --max-width: 600px;
            --padding: 50px;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--background-color);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .maintenance-container {
            background-color: var(--container-background);
            border-radius: 10px;
            box-shadow: 0 5px 15px var(--shadow-color);
            padding: var(--padding);
            text-align: center;
            width: 80%;
            max-width: var(--max-width);
        }

        .maintenance-container h1 {
            font-size: var(--heading-font-size);
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .maintenance-container p {
            font-size: var(--text-font-size);
            color: var(--secondary-color);
            margin-bottom: 30px;
        }

        .maintenance-container .message {
            font-size: var(--subheading-font-size);
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 30px;
        }

        .maintenance-container .info {
            font-size: 1rem;
            color: var(--secondary-color);
        }

        .maintenance-container .contact-button {
            padding: 12px 20px;
            background-color: var(--button-background-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .maintenance-container .contact-button:hover {
            background-color: var(--button-hover-color);
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .maintenance-container {
                padding: 30px;
            }

            .maintenance-container h1 {
                font-size: 2rem;
            }

            .maintenance-container p {
                font-size: 1rem;
            }

            .maintenance-container .contact-button {
                font-size: 0.9rem;
                padding: 10px 15px;
            }
        }

        @media (max-width: 480px) {
            .maintenance-container h1 {
                font-size: 1.8rem;
            }

            .maintenance-container .message {
                font-size: 1.2rem;
            }

            .maintenance-container .info {
                font-size: 0.9rem;
            }

            .maintenance-container .contact-button {
                font-size: 0.8rem;
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <h1>Website Under Maintenance</h1>
        <p class="message">We are currently working on improvements. Please check back later.</p>
        <p class="info">We apologize for any inconvenience. If you need assistance, feel free to <a href="mailto:support@example.com">contact support</a>.</p>
        <a href="mailto:support@example.com" class="contact-button">Contact Support</a>
    </div>
</body>
</html>
