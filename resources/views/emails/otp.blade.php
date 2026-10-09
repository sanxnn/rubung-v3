<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>{{ $title }}</title>
</head>

<body
    style="
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
        font-family: Arial, Helvetica, sans-serif;
        color: #1f2937;
    "
>

    <div
        style="
            width: 100%;
            padding: 40px 20px;
            box-sizing: border-box;
        "
    >

        <div
            style="
                max-width: 520px;
                margin: 0 auto;
                background: #ffffff;
                border-radius: 16px;
                padding: 32px;
                box-sizing: border-box;
            "
        >

            <div
                style="
                    text-align: center;
                    margin-bottom: 28px;
                "
            >

                <h1
                    style="
                        margin: 0;
                        font-size: 24px;
                        color: #111827;
                    "
                >
                    Batik Rubung Kuning
                </h1>

                <p
                    style="
                        margin: 8px 0 0;
                        color: #6b7280;
                        font-size: 14px;
                    "
                >
                    {{ $title }}
                </p>

            </div>


            <p
                style="
                    margin: 0 0 12px;
                    font-size: 15px;
                "
            >
                Halo, <strong>{{ $name }}</strong>.
            </p>


            <p
                style="
                    margin: 0 0 24px;
                    font-size: 14px;
                    line-height: 1.7;
                    color: #4b5563;
                "
            >
                {{ $description }}
            </p>


            <div
                style="
                    background: #f9fafb;
                    border-radius: 12px;
                    padding: 24px;
                    text-align: center;
                    margin-bottom: 24px;
                "
            >

                <p
                    style="
                        margin: 0 0 8px;
                        font-size: 12px;
                        color: #6b7280;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                    "
                >
                    Kode OTP
                </p>

                <div
                    style="
                        font-size: 32px;
                        font-weight: bold;
                        letter-spacing: 8px;
                        color: #111827;
                    "
                >
                    {{ $otp }}
                </div>

                <p
                    style="
                        margin: 12px 0 0;
                        font-size: 12px;
                        color: #9ca3af;
                    "
                >
                    Berlaku selama 10 menit.
                </p>

            </div>


            <p
                style="
                    margin: 0;
                    font-size: 13px;
                    line-height: 1.6;
                    color: #6b7280;
                "
            >
                Jika Anda tidak melakukan permintaan ini, abaikan email ini.
                Jangan berikan kode OTP kepada siapa pun.
            </p>


            <div
                style="
                    border-top: 1px solid #e5e7eb;
                    margin-top: 28px;
                    padding-top: 20px;
                    text-align: center;
                "
            >

                <p
                    style="
                        margin: 0;
                        font-size: 12px;
                        color: #9ca3af;
                    "
                >
                    © {{ date('Y') }} Batik Rubung Kuning
                </p>

            </div>

        </div>

    </div>

</body>

</html>
