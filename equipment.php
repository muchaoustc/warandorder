<?php
global $load_data, $props_list, $mats_list;
$props_list = [];
$mats_list  = [];
$load_data  = [];
load_data();
function load_data() {
    global $load_data, $props_list, $mats_list;
    $path  = __DIR__ . "/database/equipment/";
    $files = scandir($path);
    foreach ($files as $file) {
        $array = explode(".", $file);
        if (end($array) != "json") {
            continue;
        }
        $json      = file_get_contents($path . $file);
        $file_data = json_decode($json, true);
        foreach ($file_data as $data) {
            foreach ($data as $name => $value) {
                foreach ($value['props'] as $prop) {
                    $props_list[$prop] = 1;
                }
                foreach ($value['mats'] as $mat) {
                    $mats_list[trim($mat, "*2")] = 1;
                }
            }
        }
        $load_data = array_merge($load_data, $file_data);
    }
    $props_list = array_keys($props_list);
    $mats_list  = array_keys($mats_list);
    sort($props_list);
    sort($mats_list);
    echo "属性标签: " . PHP_EOL . join(", ", $props_list) . PHP_EOL;
    echo PHP_EOL;
    echo "材料标签: " . PHP_EOL . join(", ", $mats_list) . PHP_EOL;
    end_line();
}

function end_line() {
    echo "--------------------------------------------------------------------------" . PHP_EOL;
}

function get_equipment_by_mats($mats, $lvl = 0) {
    global $load_data;
    $result = [];
    foreach ($load_data as $item) {
        foreach ($item as $name => $value) {
            if (array_search($mats, $value['mats']) !== false) {
                if ($lvl == 0) {
                    $result[] = sprintf("{$name}-{$value['location']}");
                } else {
                    $add = "等级为{$lvl}, 且";
                    if ($lvl == $value['lvl']) {
                        $result[] = sprintf("{$name}-{$value['location']}");
                    }
                }
            }
        }
    }
    echo "{$add}使用 [{$mats}] 的装备为：" . PHP_EOL;
    echo sprintf(join(", ", $result)) . PHP_EOL;
    end_line();
}

function get_equipment_by_props($props) {
    global $load_data;
    $result = [];
    foreach ($load_data as $item) {
        foreach ($item as $name => $value) {
            if (array_search($props, $value['props']) !== false) {
                $str = str_pad(sprintf("{$name}-{$value['location']}-lv{$value['lvl']}"), 20);
                if (empty($result[$value['location']])) {
                    $result[$value['location']] = $str;
                } else {
                    $result[$value['location']] = sprintf("{$result[$value['location']]} \t|\t {$str}");
                }

            }
        }
    }
    echo "包含属性 [{$props}] 的装备为：" . PHP_EOL;
    foreach ($result as $key => $value) {
        echo sprintf("$key\n$value") . PHP_EOL . PHP_EOL;
    }
    end_line();
}

function get_equipment_with_detail($name) {
    global $load_data;
    foreach ($load_data as $value) {
        foreach ($value as $k => $v) {
            if ($k == $name) {
                echo "名称: " . $name . PHP_EOL;
                echo "等级: " . $v['lvl'] . PHP_EOL;
                echo "部位: " . $v['location'] . PHP_EOL;
                echo "材料: " . join(", ", $v['mats']) . PHP_EOL;
                echo "属性: " . join(", ", $v['props']) . PHP_EOL;
            }
        }
    }
    end_line();
}

get_equipment_by_mats("麻绳", '1');
//get_equipment_by_props("士兵招募加速");
//get_equipment_with_detail('天界铠甲');