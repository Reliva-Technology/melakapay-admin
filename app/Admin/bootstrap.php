<?php
Use Encore\Admin\Admin;

Admin::js('slug.js');
Admin::css('custom.css');

Encore\Admin\Form::forget(['map', 'editor']);
