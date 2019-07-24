<table>
<thead>
<tr>
<th align="left">Branch</th>
<th align="center">CI</th>
</tr>
</thead>
<tbody>
<tr>
<td align="left">master</td>
<td align="center"></td>
</tr>
<tr>
<td align="left">dev</td>
<td align="center"><a href='https://jenkins.acid-software.net/buildStatus/icon?job=Hackthon--Dev'><img src='https://jenkins.acid-software.net/buildStatus/icon?job=Hackthon--Dev'></a></td>
</tr>
</tbody>
</table>

**EGRESSOS IFC - Sistema de Acompanhamento e Interação de Egressos**

    O presente projeto de tem por objetivo o desenvolvimento de uma plataforma para
    acompanhamento de egressos do Instituto Federal Catarinense - IFC. Tendo como principal objetivo
    oferecer suporte para construção de indicadores educacionais estabelecendo meio de comunicação
    e interação entre os egressos e a instituição. Buscando desta maneira o atendimento às demandas
    internas no tocante a Política de Acompanhamento de Egressos bem como fomentar a coleta de
    dados sobre a inserção dos egressos no mercado de trabalho.
    Fornecendo assim, a criação e elaboração de subsídios necessários aos proponentes de cursos,
    seja para criação, revisão ou organização das propostas de formação. Para o desenvolvimento do
    projeto, está sendo utilizado como base do processo os frameworks: CodeIgniter, TWIG, Semantic
    UI, CKEditor e também outras bibliotecas como jQuery. A lista completa consta abaixo na descrição
    das técnologias utilizadas.

**Configurações e recomendações do projeto:**
>*   Utilizar um servidor local, no projeto é utilizado o Servidor Apache.
>*   Utilizar um banco de dados, no projeto é utilizado o MySQL 5.7 junto com o PhpMyAdmin.
>*   Utilizar ferramenta para versionamento, no projeto é utilizado GIT.
>*   Utilizar o Composer para ter controle das dependências do projeto.
>*   Utilizar Kanban para controle de tarefas a serem desenvolvidas ou que estejam em desenvolvimento, no projeto é utilizado o Trello.
>*   Utilizar a versão mais recente e estável do PHP, no projeto atualmente está sendo utilizado o PHP7.3
>*   Utilizar um ambiente de trabalho para desenvolvimento, no projeto é utilizado o PhpStorm 2019.1
>*   *Para otimizar o projeto, deve ser feita uma configuração no apache, onde deve ser adicionado o seguinte código em 000-default.conf, apache2.conf ou httpd.conf, abaixo da linha DocumentRoot:
    
*       <Directory "/var/www/html">
            AllowOverride All
        </Directory>



**Dependências do projeto:**
*   Codeigniter
    [Link](https://www.codeigniter.com/user_guide/)
    
*   Twig
    [Link](https://twig.symfony.com/doc/2.x/)

*   Hybridauth
    [Link](https://hybridauth.github.io/documentation.html)

*   CKEditor
    [Link](https://ckeditor.com/)

    


**Funcionalidades do projeto:**
*       Cadastro de usuário:
                Descrição:
                O cadastro de usuário é feito através de um formulário, onde o usuário deve informar em qual campus estuda, para que seja feita a filtragem dos cursos que já foram disponibilizados.
            Composição:
                É utilizado o helper "form", para criar o formulário de cadastro e suas validações, assim como as mensagens de retorno de erro. Após a validação, é enviado por email um link para a
                ativação da conta, sendo um link único que é cadstrado no banco de dados, depois da ativação é permitido o login. O cadastro pode ser feito também utilizando o Facebook e LinkedIn,
                já que é capturada algumas informações públicas, como por exemplo, nome, email e foto de perfil. 
                
*       Login:
            Descrição:
                O login é utilizado por quem já está cadastrado no sistema, efetuando o login, você tem muitas outras funcionalidades para usar, o login também é usado para fazer um controle de 
                quem está acessado o sistema e quem está usando as demais funcionalidades. O login pode ser feito de duas maneiras, dependendo de como o usuário fez o seu cadastro.
            Composição:
                Caso o usuário tenha feito o cadastro atráves do Facebook ou LinkedIn, é utilizada a biblioteca "HybridAuth" para processar o login do usuário. Caso o cadastro tenha sido feito 
                manualmente então o suário utiliza o email cadastrado e a senha.
           
*       Cadastro de Post:
            Descrição:
                O cadastro de post é feito por um formulário, contendo título, mensagem e fotos.
            Composição:
                É utilizado o helper "form", para criar o formulário e a biblioteca "form_validation", para fazer as validaçoẽs do formulário, depois é utilizado a biblioteca "custom_upload", para 
                fazer o upload de múltiplos arquivos. Todos os posts são salvos no banco de dados.
                
*       Adicionar amigo:
            Descrição:
                Você pode adicionar um amigo para poder entrar em contato, uma funcionalidade futura, seria o chat.
            Composição:
                
*       Enviar email para usuário:
            Descrição:
                O envio de email é feito apenas na página do administrador, para que não se tenha invasão de privacidade, onde o admnistrador pode enviar emails para usuários específicos. 
            Composição:
                Para o envio de emal, é gerado um formulário com o helper "form", depois é filtrado os usuários atráves de um campo de busca utiliando jquery e ajax para atualizar os usuários 
                automaticamente sem perder os dados da pesquisa, depois é enviado o email utilizando os padrões do codeigniter. 
       
*       Enviar email para grupo:
            Descrição:
                Segue o mesmo estilo do envio de email para usuário, porém é selecionado um campus e o email é enviado para todos do grupo.
            Composição:
                O grupo é selecionado atráves de uma busca com Ajax, após selecionado o grupo é gerado um formulário de envio, no envio é gerado uma lista com o email de todos os usuários e a função
                de envio percorre toda essa lista, enviando de maneira separada cada email.



        