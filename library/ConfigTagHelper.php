<?php
use Power\Models\Menus;

class ConfigTagHelper {

    public static function homeUrlSelect() {
        $flatMenus = Menus::getFlatMenus();
        $options = [];
        foreach ($flatMenus as $key => $menu) {
            $url = $menu['url_suffix'] ?: (PA_URL_PATH.'menu/'.$menu['menu_id'].'/index');
            $isGroup = empty($menu['router']) ? 1 : 0;

            $symbol = str_repeat('　', $menu['level']);
            $item['text'] = $symbol . ($menu['level'] && !$isGroup ? '>' : '') . $menu['name'];
            $item['isgroup'] = $isGroup;
            $item['value'] = $url;           

            $options[] = $item;
        }

        return $options;
    }
}