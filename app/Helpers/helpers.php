<?php

if (! function_exists('daily_background')) {
    function daily_background()
    {
        $imagens = [
            'frontend/img/login/b1.png',
            'frontend/img/login/b2c.png',
            'frontend/img/login/b3.png',
            'frontend/img/login/video2.mp4',
            'frontend/img/login/b4.png',
            'frontend/img/login/b5.png',
            'frontend/img/login/b6.png',
            'frontend/img/login/video.mp4',
        ];

        // Calcula um "bloco de 3 minutos" com base no horário atual
        $slot = floor(time() / (60 * 5)); // 5 minutos em segundos

        // Usa o slot como seed (sempre o mesmo dentro de cada intervalo de 3 minutos)
        srand($slot);

        return asset($imagens[array_rand($imagens)]);
    }
}
