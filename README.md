# Iandé
Plugin WordPress para agendamento de visita a museus

## Instalação

## Configuração

## Utilização

## API

# Contribuindo
## Subindo ambiente de desenvolvimento

## Estrutura do plugin
Os *slots* são criados a partir dos horários e da duração das visitas. Ver https://www.php.net/manual/pt_BR/class.dateperiod.php#109846

### Configurações

- **duracao**: `integer` - duração em minutos das visitas.
- **tamanho_grupo**: `integer` - número máximo de pessoas por grupo.
- **grupos_slot**: `integer` - número máximo de grupos por slot.
- **horarios**: `object` 
    ```
    // exemplo de valor da opção horários
    {
        dom: [
            { from: "09:00", to: "12:00" },
            { from: "13:30", to: "18:00" },
            { from: "19:00", to: "22:00" }
        ],
        seg: [], // fechado
        ter: [
            { from: "09:00", to: "12:00" },
            { from: "13:30", to: "18:00" }
        ]
    }
    ```
- **fechado**: `{reason, detail, from, to}[]`
    ```
    // exemplo da opção fechado
    [
        {
            reason: "Carnaval",
            detail: "Texto explicando o motivo do fechamento",
            from: "2020-02-22",
            to: "2020-02-26"
        },
        {
            reason: "Pandemia",
            detail: "Texto explicando o motivo do fechamento",
            from: "2020-03-20",
            to: null // indeterminado, o museu não aceita novos agendamentos enquanto não for definido esse valor ou removida essa item do array
        }
    ]
    ```

### Post Types
- **agendamento** - Post Type Agendamento

  por ser opcional, o nome da visita não pode ser o *post_title*. definir o nome do *post_title* como `"{nome-responsavel} - {nome-grupo} - {data} {horário}"` 
  
  metadados:

  - **objetivo**: `select`
  - **nome**: `string` (opcional)
  - **data**: `date`
  - **horário**: `time`
  - **responsavel_nome**: `string`
  - **responsavel_sobrenome**: `string`
  - **responsavel_email**: `string`
  - **responsavel_telefone**
  - **grupo_nome**: `string`
  - **grupo_num_pessoas**: `integer`
  - **grupo_escolaridade**: `select`
  - **grupo_num_responsaveis**: `integer`
  - **grupo_idioma**: `select[]`
  - **grupo_deficientes**: `{tipo, num_pessoas}[]`
    ```
    // exemplo de entrada do grupo_deficiente
    [
        {
            tipo: 'paralisia cerebral',
            num_pessoas: 2
        }
    ]
    ```
  - **grupo_preparado**: `boolean`
  - **grupo_preparado_descricao**: `string`
  
  - **ja_visitou_museu**: `boolean`


- **instituicao** - Post Type Instituição
  - **cnpj**: `string` (opcional)
  - **perfil**: `select`
  - **telefone**: `string`
  - **email**: `string`
  - **endereco_cep**: `
  - **endereco_logradouro**:
  - **endereco_numero**:
  - **endereco_complemento**:
  - **endereco_bairro**:
  - **enderco_uf**:
  - **endereco_cidade**


## Endpoints
- `/iande/user/is_logged_in` - verfica se o usuário está logado
- `/iande/user/get_logged_in` - retorna o usuário logado
- `/iande/user/login` **{email,password}** - tenta fazer o login do usuário
- `/iande/user/logout` - desloga o usuário
- `/iande/user/create` **{first_name,last_name,phone,email,password}** - tenta criar o usuário com os dados informados. Após a criação o usuário é autenticado.
  
## Api de hooks para extensões

### ações
- `iande.route.{controller}/{action}` **(array $params)** - executado antes da execução da rota
- `iande.route_not_found` **(string $controller, string $action, array $params)** - executado antes da renderização do 404
- `iande.login_before` **(array $params)** - executado antes da tentativa de login
- `iande.login_success` **(WP_User $user)** - executado após login bem-sucedido
- `iande.login_fail` **(array $params)** - executado após login mal-sucedido
- `iande.before_create_user` **(array $params)**
- `iande.after_create_user` **(array $params)**

### filtros
- `iande.parse_user` **($parsed_user, WP_User $user)** - filtra o valor parseado do usuário
- `iande.route.{controller}/{action}.params` **($params)** - filtra os parâmetros passados para a view ou endpoint
  