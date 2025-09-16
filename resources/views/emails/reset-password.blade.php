<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Recuperação de Senha - Taiksu Office</title>
</head>

<body style="margin: 50px;; padding:0; font-family: Arial, sans-serif; background-color:#F3F8F3; color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="500" cellpadding="0" cellspacing="0"
                    style="background:#fff; overflow:hidden;">

                    <!-- Cabeçalho -->
                    <tr>
                        <td align="left" style="background:#ebebeb; padding:30px;">
                            <img src="{{ asset('frontend/img/office_logo.png') }}" style="height:20px;">
                        </td>
                    </tr>

                    <!-- Corpo -->
                    <tr>
                        <td style="padding-top: 80px; padding-bottom: 80px; padding-left: 30px; padding-right: 30px; text-align: center;">
                            <p style="font-size:16px; line-height:1.6; margin-bottom:20px;">
                                Você está recebendo este e-mail porque recebemos uma solicitação de <strong>recuperação
                                    de senha</strong> para a sua conta no
                                <strong>Taiksu Office</strong>.
                            </p>

                            <!-- Botão -->
                            <p style="text-align:center; margin:30px 0;">
                                <a href="{{ $url }}"
                                    style="background:#2c8516; color:#fff; text-decoration:none; padding:12px 24px; border-radius:8px; font-weight:bold; font-size:16px; display:inline-block;">
                                    Redefinir Senha
                                </a>
                            </p>
                        </td>
                    </tr>

                    <!-- Rodapé -->
                    <tr>
                        <td style="background:#eaf7e9; text-align:center; padding:15px; font-size:12px; color:#666;">
                            Se você estiver com problemas para clicar no botão, copie e cole o link abaixo no seu navegador:
                            <br>
                            <a href="{{ $url }}"
                                style="color:#195b09; word-break:break-all;">{{ $url }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
