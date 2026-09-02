<?php

declare(strict_types=1);

namespace Stl\EboardUi\Bridge\Yii2;

use Stl\EboardUi\Contracts\Renderable;

if (class_exists(\yii\base\Widget::class)) {
    final class Widget extends \yii\base\Widget
    {
        public Renderable $component;

        public function run(): string
        {
            return $this->component->render();
        }
    }
}
