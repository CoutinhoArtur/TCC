<?php
/* ==========================================================
   ORIENTAÇÕES DO FLUXO — SITEMATCH
   ==========================================================
   Textos explicativos por perfil/status. NÃO faz transição
   de estado nem substitui autorizações dos Models e rotas.
   ========================================================== */
if (!function_exists('smOrientacaoProposta')) {
    function smOrientacaoProposta(string $status, string $perfil, bool $temContratacao = false): string
    {
        if ($temContratacao) {
            return 'Proposta aceita. A negociação passou para o acompanhamento da contratação.';
        }
        $mensagens = [
            'cliente' => [
                'preliminar' => 'Esta é a proposta inicial. Converse com o desenvolvedor para esclarecer escopo, preço e prazo. O aceite ficará disponível quando ele enviar a proposta definitiva.',
                'em_negociacao' => 'Negociação em andamento. Utilize o chat para combinar os detalhes e aguarde a proposta definitiva para poder aceitar.',
                'aguardando_revisao' => 'O desenvolvedor precisa revisar a proposta. Continue a conversa e aguarde o envio da versão definitiva.',
                'definitiva' => 'Proposta definitiva recebida. Confira preço, prazo, descrição e entregáveis. Depois você poderá aceitar ou recusar.',
                'aceita' => 'Proposta aceita. Acompanhe o andamento do serviço na área de contratações.',
                'recusada' => 'Esta proposta foi recusada e não pode mais ser aceita.',
                'encerrada' => 'Esta negociação foi encerrada porque outra proposta foi escolhida.',
                'cancelada' => 'Esta proposta foi cancelada.',
            ],
            'desenvolvedor' => [
                'preliminar' => 'Proposta preliminar enviada. Converse com o cliente e, quando o escopo estiver definido, envie a proposta definitiva.',
                'em_negociacao' => 'O cliente está negociando os detalhes. Use o chat e finalize a proposta quando preço, prazo e entregáveis estiverem alinhados.',
                'aguardando_revisao' => 'Sua proposta precisa ser revisada. Confira os detalhes no chat e envie uma proposta definitiva atualizada.',
                'definitiva' => 'Sua proposta definitiva foi enviada. Aguarde a decisão do cliente; você pode continuar conversando pelo chat.',
                'aceita' => 'Proposta aceita! Acompanhe a contratação para iniciar e entregar o projeto.',
                'recusada' => 'O cliente recusou esta proposta.',
                'encerrada' => 'Esta negociação foi encerrada porque o cliente escolheu outra proposta.',
                'cancelada' => 'Esta proposta foi cancelada.',
            ],
        ];
        return $mensagens[$perfil][$status] ?? 'Consulte o chat para acompanhar os próximos passos.';
    }
}
if (!function_exists('smOrientacaoContratacao')) {
    function smOrientacaoContratacao(string $status, string $perfil, bool $temAvaliacao = false, bool $temAjustes = false): string
    {
        $mensagens = [
            'cliente' => [
                'ativa' => 'Proposta aceita! Aguarde o desenvolvedor iniciar o projeto. Você pode conversar pelo chat.',
                'em_andamento' => 'O desenvolvedor está trabalhando no projeto. Acompanhe a negociação pelo chat e aguarde a entrega.',
                'ajustes_solicitados' => 'Os ajustes foram solicitados. Aguarde o desenvolvedor realizar as correções e enviar uma nova entrega.',
                'aguardando_confirmacao' => 'Entrega recebida! Analise o que foi combinado. Confirme a conclusão se estiver tudo certo ou descreva os ajustes necessários.',
                'concluida' => $temAvaliacao ? 'Projeto concluído e avaliado. Você pode consultar o histórico.' : 'Projeto concluído. Falta apenas avaliar o desenvolvedor.',
                'cancelada' => 'A contratação foi cancelada.',
            ],
            'desenvolvedor' => [
                'ativa' => 'O cliente aceitou a proposta. Clique em “Iniciar projeto” quando começar o trabalho.',
                'em_andamento' => $temAjustes ? 'O cliente solicitou ajustes. Revise a descrição abaixo e envie uma nova entrega quando terminar.' : 'Projeto em andamento. Quando finalizar os entregáveis combinados, clique em “Sinalizar entrega”.',
                'ajustes_solicitados' => 'O cliente solicitou ajustes. Faça as correções e sinalize uma nova entrega.',
                'aguardando_confirmacao' => 'Entrega sinalizada. Aguarde o cliente confirmar a conclusão ou solicitar ajustes.',
                'concluida' => 'Projeto concluído. Consulte o histórico e a avaliação recebida.',
                'cancelada' => 'A contratação foi cancelada.',
            ],
        ];
        return $mensagens[$perfil][$status] ?? 'Acompanhe o andamento desta contratação.';
    }
}
if (!function_exists('smPassoContratacao')) {
    function smPassoContratacao(string $status): int
    {
        return match ($status) {
            'ativa' => 1,
            'em_andamento', 'ajustes_solicitados' => 2,
            'aguardando_confirmacao' => 3,
            'concluida' => 4,
            default => 0,
        };
    }
}
if (!function_exists('smNomeStatus')) {
    function smNomeStatus(string $status): string
    {
        $mapa = [
            'ativa'=>'Aguardando início', 'em_andamento'=>'Em andamento',
            'aguardando_confirmacao'=>'Aguardando confirmação',
            'ajustes_solicitados'=>'Ajustes solicitados',
            'concluida'=>'Concluída', 'concluido'=>'Concluído',
            'cancelada'=>'Cancelada', 'cancelado'=>'Cancelado',
            'preliminar'=>'Proposta inicial', 'em_negociacao'=>'Em negociação',
            'aguardando_revisao'=>'Aguardando revisão', 'definitiva'=>'Proposta definitiva',
            'aceita'=>'Aceita', 'recusada'=>'Recusada', 'encerrada'=>'Encerrada',
            'rascunho'=>'Rascunho', 'aberto'=>'Aberto', 'contratado'=>'Contratado',
        ];
        return $mapa[$status] ?? str_replace('_', ' ', ucfirst($status));
    }
}
