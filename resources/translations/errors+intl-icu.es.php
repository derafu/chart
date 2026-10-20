<?php

declare(strict_types=1);

/**
 * Derafu: Chart - PHP Chart Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

return [
    'The first dataset requires at least one point for this chart.' =>
        'El primer conjunto de datos requiere al menos un punto para este gráfico.',
    'The chart requires at least one dataset with points before it can be rendered.' =>
        'El gráfico requiere al menos un conjunto de datos con puntos antes de poder renderizarse.',

    'Chart renderer class {renderer} does not exist.' =>
        'La clase del renderizador del gráfico {renderer} no existe.',
    'Chart renderer class {renderer} must implement {interface}.' =>
        'La clase del renderizador del gráfico {renderer} debe implementar {interface}.',
    'Could not create chart renderer {renderer}: {error}' =>
        'No se pudo crear el renderizador del gráfico {renderer}: {error}',

    'Invalid hexadecimal color {color}: expected six hexadecimal digits with an optional leading #.' =>
        'Color hexadecimal {color} inválido: se esperaban seis dígitos hexadecimales con un # inicial opcional.',
    'Could not allocate chart color {color}.' =>
        'No se pudo reservar el color {color} del gráfico.',
    'Could not allocate chart color {color} with alpha {alpha}.' =>
        'No se pudo reservar el color {color} del gráfico con alfa {alpha}.',
    'Could not measure chart text using font {font}.' =>
        'No se pudo medir el texto del gráfico con la fuente {font}.',

    'Invalid chart option {option}: expected {expected}; {type} given.' =>
        'Opción de gráfico {option} inválida: se esperaba {expected}; se recibió {type}.',
    'Chart output format {format} is not supported by the renderer.' =>
        'El formato de salida del gráfico {format} no está soportado por el renderizador.',
    'No valid TTF font found.' =>
        'No se encontró una fuente TTF válida.',
    'The chart requires at least one dataset before it can be rendered.' =>
        'El gráfico requiere al menos un conjunto de datos antes de poder renderizarse.',
    'The minimum scale value cannot be greater than the maximum.' =>
        'El valor mínimo de la escala no puede ser mayor que el máximo.',
    'Chart dimensions must be positive.' =>
        'Las dimensiones del gráfico deben ser positivas.',
];
