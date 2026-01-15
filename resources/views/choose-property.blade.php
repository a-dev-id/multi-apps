{{-- resources/views/choose-property.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose Property</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #050608;
            --panel: #0f131a;
            --line: rgba(255, 255, 255, .08);
            --text: rgba(255, 255, 255, .9);
            --muted: rgba(255, 255, 255, .6);
            --gold: #a67c3d;
            --green: #3aa981;
            --radius: 18px;
            --shadow: 0 30px 80px rgba(0, 0, 0, .55);
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Inter, system-ui, sans-serif;
            color: var(--text);
            background:
                radial-gradient(900px 500px at 50% 20%, rgba(166, 124, 61, .18), transparent 60%),
                linear-gradient(180deg, #050608, #0b0d12);
            padding: 28px;
        }

        .wrapper {
            width: min(1000px, 100%)
        }

        .header {
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0 0 6px;
            font-size: 22px;
            letter-spacing: -.02em;
        }

        .header p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }

        .panel {
            background: linear-gradient(180deg, rgba(255, 255, 255, .02), rgba(255, 255, 255, .01));
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 18px;
            box-shadow: var(--shadow);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        @media(max-width:800px) {
            .grid {
                grid-template-columns: 1fr
            }
        }

        .card {
            position: relative;
            padding: 24px;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            background:
                radial-gradient(500px 200px at 80% 0%, rgba(255, 255, 255, .08), transparent 60%),
                linear-gradient(180deg, rgba(255, 255, 255, .02), rgba(255, 255, 255, .01));
            transition: .2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, .18);
        }

        .logo {
            height: 46px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
        }

        .logo img {
            max-height: 100%;
            max-width: 240px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .badge {
            display: inline-block;
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--line);
            color: var(--muted);
            margin-bottom: 10px;
        }

        .title {
            margin: 0 0 6px;
            font-size: 20px;
        }

        .desc {
            margin: 0 0 18px;
            font-size: 13.5px;
            color: var(--muted);
            line-height: 1.45;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .16);
            color: #fff;
            background: rgba(0, 0, 0, .25);
        }

        .btn:hover {
            background: rgba(0, 0, 0, .35);
        }

        .btn-gold {
            border-color: rgba(166, 124, 61, .45);
            background: linear-gradient(180deg, rgba(166, 124, 61, .25), rgba(166, 124, 61, .1));
        }

        .btn-green {
            border-color: rgba(58, 169, 129, .45);
            background: linear-gradient(180deg, rgba(58, 169, 129, .25), rgba(58, 169, 129, .1));
        }

    </style>
</head>

<body>
    <div class="wrapper">

        <div class="header">
            <h1>Choose Property</h1>
            <p>Select which property you want to manage</p>
        </div>

        <div class="panel">
            <div class="grid">

                {{-- Hanging Gardens of Bali --}}
                <div class="card">
                    <div class="logo">
                        <img src="https://hanginggardensofbali.com/images/logo.png" alt="Hanging Gardens of Bali">
                    </div>

                    <span class="badge">Iconic Resort</span>
                    <h2 class="title">Hanging Gardens of Bali</h2>
                    <p class="desc">
                        Signature luxury resort with premium guest communication and marketing campaigns.
                    </p>

                    <div class="actions">
                        <a class="btn btn-gold" href="#">Newsletter →</a>
                        <a class="btn" href="#">Guest Letter →</a>
                    </div>
                </div>

                {{-- Nandini Jungle --}}
                <div class="card">
                    <div class="logo">
                        <img src="https://nandinibali.com/images/logo-njhg.png" alt="Nandini Jungle by Hanging Gardens">
                    </div>

                    <span class="badge">Jungle Retreat</span>
                    <h2 class="title">Nandini Jungle by Hanging Gardens</h2>
                    <p class="desc">
                        Nature-immersive jungle escape with targeted guest journeys and seasonal offers.
                    </p>

                    <div class="actions">
                        <a class="btn btn-green" href="/nandini/newsletter/">Newsletter →</a>
                        <a class="btn" href="/nandini/guestletter/">Guest Letter →</a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</body>

</html>