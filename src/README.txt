=== Iandé ===
Contributors: percebeeduca
Tags: Reservation system, scheduling, schedule, group visits, virtual group tours, museums, Tainacan
Requires at least: 5.5.3
Tested up to: 5.7
Requires PHP: 7.2
Stable tag: 0.7.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Iandé, plugin de agendamento de visitas de grupos para instituições que recebem públicos presencial ou digitalmente.

== Descrição ==

Iandé é um plugin do WordPress, voltado para o agendamento de visitas de grupos, para instituições que recebem públicos presencial ou digitalmente.

O Iandé vem atender algumas das principais demandas de museus e instituições culturais, permitindo a automatização do agendamento das visitas de grupos, escolares e não escolares, avaliação da experiência de visita e ainda a geração de relatórios qualificados sobre os visitantes e as dinâmicas dos grupos. O Iandé é um software livre  e tem como pressuposto a democratização do acesso às tecnologias digitais pelos museus e instituições culturais. A interface amigável visa ampliar o engajamento do público e facilitar o uso pelos administradores institucionais.

O Iandé será interligado com o plugin Tainacan e permitirá que os visitantes escolham os acervos e customizem roteiros de visita no momento da realização do agendamento

Features:

* Agendamento de grupos de forma automatizada, facilitando a marcação da visita pelo público e o controle do fluxo de visitantes pela instituição.
* Instância administrativa de fácil utilização pela equipe, permitindo alterações e customizações da interface conforme as necessidades institucionais.
* Instância de mediação com os visitantes customizável, permitindo a remodelação da comunicação desejada.
* Geração de relatórios de dados sobre perfil do grupo e avaliação da experiência da visita (em implantação).
* Módulo de check-in e avaliação, permitindo a confirmação dos dados inseridos pelos públicos no momento do agendamento (em implantação).
* Interligação com o plugin Tainacan, permitindo a seleção prévia de acervos de interesse e a organização de roteiros de visita (em implantação).


== Instalação ==

1. Carregar `iande.php` à pasta `/wp-content/plugins/`;
2. Ativar o the plugin por meio do menu 'Plugins' menu no WordPress;
3. Selecionar Iandé no menu do Painel administrador Wordpress;


== Screenshots ==

1. Iandé expõe cinco post types para o usuário: Agendamentos, Instituições, Exposições, Grupos e Exceções. Em destaque, a tela de edição e aprovação de agendamento
2. Tela de edição de exposição, com calendário de horários disponível e agendamentos
3. Tela de edição de exceções de horários
4. Tela de configuração de vocabulários dos agendamentos
5. Tela de configuração dos títulos, corpos e assinaturas dos e-mails para visitantes
6. Tela de login para visitantes
7. Tela de listagem de agendamentos do visitante
8. Tela de listagem de instituições do visitante


== Changelog ==

= 0.7.5 =
* Preparar plugin para lançamento na loja
* Instituições cadastradas são visíveis para outros visitantes
* Correção de bug: validação de campos do check-in

= 0.7.4 =
* Correção de bug: assinalar instituição a visitas não-institucionais

= 0.7.3 =
* Adicionar source maps

= 0.7.2 =
* Exibir exposições privadas no calendário do educador
* Desabilitar botão "Ver" para post-types do Iandé
* Melhora na exibição de posts cancelados na listagem de agendamentos e grupos

= 0.7.0 =
* Permitir a usuários administradores alternar entre modo de visitante e educador
* Adicionar link de check-in ao menu
* Adicionar aviso de perfil do usuário administrador está incompleto
* Permitir adicionar motivo no e-mail de cancelamento
* Correção de bug: adicionar tooltip à etapa correta
* Correção de bug: bloquear agendamentos antes do início da exposição ou depois de seu fim
* Correção de bug: grupos recém-criados não travam horário

= 0.6.0 =
* Suporte para check-in, avaliação pelo educador e avaliação pelo usuário
* Confirmação de cancelamento de agendamento pelo visitante

= 0.5.2 =
* Preparação para o suporte para check-in
* Suporte experimental à CAPTCHA invisível

= 0.5.1 =
* Adicionar link do front-end ao menu único
* Mensagem de erro ao apagar agendamentos pendentes ou aprovados
* Correção de bug: cancelar agendamento libera vaga para novos agendamentos
* Correção de bug: não permitir voltar à página anterior se os grupos não estiverem preenchidos

= 0.5.0 =
* Menu único reunindo post-types e configuração do plugin
* Configuração do prazo mínimo para agendamento

= 0.4.0 =
* Cancelamento de agendamentos por parte do usuário
* Melhoria na velocidade de carregamento das páginas
* Correção de bug: permitir agendamento se horário final da visita coincidir com fechamento do museu

== Mais informações ==

Visite nosso site oficial: [Iandé](https://iandecultura.com.br/)
Contribua com o código fonte:: [GitLab Iandé](https://gitlab.com/percebe/iande-plugin/)
