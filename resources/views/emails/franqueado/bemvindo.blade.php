@component('mail::message')
# Bem-vindo, {{ $user->name }}

Sua conta de **Franqueado** foi criada com sucesso!

### Credenciais de acesso:
- **E-mail:** {{ $user->email }}
- **Senha:** {{ $plainPassword }}

@component('mail::button', ['url' => 'https://login.taiksu.com.br/'])
Acessar o sistema
@endcomponent

Se esquecer a senha, você pode redefinir em:
[Recuperar Senha](https://login.taiksu.com.br/forgot-password)

Obrigado,<br>
Equipe Taiksu
@endcomponent
