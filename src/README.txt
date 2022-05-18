=== Iandé ===
Contributors: percebeeduca
Tags: Reservation system, scheduling, schedule, group visits, virtual group tours, museums, Tainacan
Requires at least: 5.5.3
Tested up to: 5.9.3
Requires PHP: 7.2
Stable tag: 0.15.5
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

1. Iandé expõe cinco post types para o usuário: Agendamentos, Instituições, Exposições, Grupos e Horários Especiais. Em destaque, a tela de edição e aprovação de agendamento
2. Tela de edição de exposição, com calendário de horários disponível e agendamentos
3. Tela de edição de exceções de horários
4. Tela de configuração de vocabulários dos agendamentos
5. Tela de configuração dos títulos, corpos e assinaturas dos e-mails para visitantes
6. Tela de login para visitantes
7. Tela de listagem de agendamentos do visitante
8. Tela de listagem de instituições do visitante


== Changelog ==

= 0.15.5 =
* Melhoria na mensagem de vagas remanescentes para agendamento

= 0.15.4 =
* Renomeia "alunos" para "estudantes"

= 0.15.2 =
* Adicionar source maps

= 0.15.1 =
* Melhoria na mensagem de sucesso de agendamento

= 0.15.0 =
* Torna nome da visita campo obrigatório
* Melhoria na descrição de quantidade de grupos durante agendamento

= 0.14.0 =
* Unifica as duas etapas do agendamento, removendo a abstração de "rascunho" para o visitante
* Permite editar agendamentos pendentes
* Versão imprimível da lista exibe grupos com mais de duas semanas de antecedência
* Adiciona link para a página da política de privacidade, se disponível
* Adiciona botão de voltar às páginas de check-in e avaliação
* Exibe dados de avaliações anteriores

= 0.13.2 =
* Adiciona mensagem de sucesso após atualização de roteiro
* Melhoria na estilização de tooltips
* Correção de bug: agendamento de visita no último dia da exposição
* Correção de bug: agendamento de visita aos domingos
* Correção de bug: calendário da exposição exibe horários apenas nos dias da exposição

= 0.13.1 =
* Correção de bug: suporte ao Tainacan v0.18.7

= 0.13.0 =
* Renomeia "exceções" para "horários especiais"
* Auto-realização de check-in para visitas com mais de 10 dias
* Auto-expiração de rascunhos com mais de 10 dias
* Desabilita botão de publicação nativo do WordPress para grupos pendentes
* Adiciona customização do número máximo de responsáveis ao nível de exposição
* Balanceia o número inicial de visitantes entre os grupos
* Correção de bug: formatação de datas nos relatórios
* Correção de bug: texto faltante no formulário de check-in
* Correção de bug: cancelamento de visitas com check-in

= 0.12.1 =
* Correção de bug: contagem de itens no roteiro

= 0.12.0 =
* Exibe número de vagas disponíveis durante agendamento
* Exibe descrição da exposição mesmo se apenas uma exposição está disponível
* Adiciona ao logo do Iandé link para página de boas-vindas
* Adiciona ao menu do front-end botão para voltar ao admin do WordPress
* Adiciona link para feedback do usuário na listagem de agendamentos
* Correção de bug: grupos que não compareceram não devem ser avaliados
* Correção de bug: agendamentos passados devem ser exibidos
* Correção de bug: tamanho mínimo para grupos é exibido corretamente

= 0.11.0 =
* Adiciona novo role "Educador do Iandé", renomeia "Administrador do Iandé" para "Coordenador do Iandé"
* Adiciona quantidade de pessoas e faixa etária à versão imprimível da lista de grupos
* Nova visualização do calendário da exposição, mostrando agendamentos pendentes
* Melhora placeholder para o nome do agendamento
* Correção de bug: opção padrão dos campos de seleção do admin devem ser vazios

= 0.10.2 =
* Correção de bug: suporte às novas versões do Tainacan

= 0.10.0 =
* Versão imprimível da lista de grupos
* Adicionar contagem de likes e visualizações aos roteiros
* Adicionar âncores internas aos itens dos roteiros
* Correção de bug: upload de CSV

= 0.9.0 =
* Implementação inicial de roteiros virtuais

= 0.8.3 =
* Filtrar dados do relatórios por exposição
* Correção de bug: pré-seleção de estado no gráfico de visitas por cidade

= 0.8.2 =
* Correção de bug: tolerância a grupos sem agendamento explícito (legados)

= 0.8.1 =
* Remover source maps

= 0.8.0 =
* Página de relatórios
* Exportação de dados via CSV

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

* Leia nosso [manual do usuário](https://iandecultura.com.br/wp-content/uploads/2021/07/Manual-de-Usabilidade-do-Site-IAND%C3%89-Museus.pdf)
* Visite nosso site oficial: [Iandé](https://iandecultura.com.br/)
* Contribua com o código fonte: [GitLab](https://gitlab.com/percebe/iande-plugin/)
