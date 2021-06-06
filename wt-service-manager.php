<?php
/*
Plugin Name: Менеджер услуг
Plugin URI: https://github.com/Donaldismo/WP_ServiceManager/
Description: Управление услугами и ценами с использованием contact form 7
Version: 0.2
Author: Александр Голубин
*/

require ('admin_panel.php');
require ('wt-service-widget.php');

class WtServiceManager_
{
    var $admin;

    function __construct()
    {
        //услуги
        add_action('init', array(
            $this,
            'register_post_type_service'
        ));
        add_action('init', array(
            $this,
            'register_taxonomy_service_category'
        ) , 0);
        add_filter('post_updated_messages', array(
            $this,
            'post_type_service_messages'
        ));

        //заявки
        if (false) //false если заявки не нужны
        
        {
            add_action('init', array(
                $this,
                'register_post_type_request'
            ));
            add_filter('post_updated_messages', array(
                $this,
                'post_type_request_messages'
            ));
        }

        // Подключаем виджет
        add_action('widgets_init', array(
            $this,
            'widget_init'
        ));

        // Регистрируем шорткод и хук для него
        add_shortcode('wp_service_table', array(&$this,
            'shortcode_service_table_action'
        ));

        // Подключаем панель администратора
        if (defined('ABSPATH') && is_admin())
        {
            $this->admin = new WtServiceManagerAdmin_();
            /*
            add_action('admin_menu', 'notification_bubble_in_admin_menu');
            
            function notification_bubble_in_admin_menu() {
            global $menu;
            $newitem = wp_count_posts( 'request' )->publish;
            $menu[22][0] .= $newitem ? "<span class='update-plugins count-1'><span class='update-count'>$newitem </span></span>" : '';
            
            }*/
        }
    }

    public static function basename()
    {
        return plugin_basename(__FILE__);
    }

    // Регистрация типа постов "Услуги"
    function register_post_type_service()
    {
        $labels = array(
            'name' => 'Услуги',
            'singular_name' => 'Услугу', // админ панель Добавить->Функцию
            'add_new' => 'Добавить услугу',
            'add_new_item' => 'Добавить новую услугу', // заголовок тега <title>
            'edit_item' => 'Редактировать услугу',
            'new_item' => 'Новая услуга',
            'all_items' => 'Все услуги',
            'view_item' => 'Просмотр услуги на сайте',
            'search_items' => 'Искать услугу',
            'not_found' => 'Услуг не найдено.',
            'not_found_in_trash' => 'В корзине нет услуг.',
            'menu_name' => 'Услуги'
            // ссылка в меню в админке
            
        );
        $args = array(
            'labels' => $labels,
            'public' => false, //если true то будет отображаться в ссылках на сайте
            'show_ui' => true, // показывать интерфейс в админке
            'has_archive' => true,
            'menu_icon' => 'dashicons-book', // иконка в меню
            'menu_position' => 21, // порядок в меню
            'supports' => array(
                'title',
                'editor',
                'revisions',
                'page-attributes'
            ) ,
            'taxonomies' => array(
                'service_type'
            )
        );
        register_post_type('service', $args);
    }
    // Регистрация типа постов "Заявки"
    function register_post_type_request()
    {
        $labels = array(
            'name' => 'Заявки',
            'singular_name' => 'Заявку', // админ панель Добавить->Функцию
            'add_new' => 'Добавить заявку',
            'add_new_item' => 'Добавить новую заявку', // заголовок тега <title>
            'edit_item' => 'Редактировать заявку',
            'new_item' => 'Новая заявка',
            'all_items' => 'Все заявки',
            'view_item' => 'Просмотр заявки на сайте',
            'search_items' => 'Искать заявку',
            'not_found' => 'Заявок не найдено.',
            'not_found_in_trash' => 'В корзине нет заявок.',
            'menu_name' => 'Заявки'
            // ссылка в меню в админке
            
        );
        $args = array(
            'labels' => $labels,
            'public' => false, //если true то будет отображаться в ссылках на сайте
            'show_ui' => true, // показывать интерфейс в админке
            'has_archive' => true,
            'menu_icon' => 'dashicons-cart', // иконка в меню
            'menu_position' => 22, // порядок в меню
            'supports' => array(
                'title',
                'editor',
                'revisions',
                'page-attributes'
            ) ,
            'taxonomies' => array(
                'request_type'
            )
        );
        register_post_type('request', $args);
    }

    // Регистрация таксономии "Категория услуг"
    function register_taxonomy_service_category()
    {

        register_taxonomy('service_category', array(
            'service'
        ) , array(
            'hierarchical' => true, /* true - по типу рубрик, false - по типу меток, по умолчанию - false */
            'labels' => array(
                /* ярлыки, нужные при создании UI, можете
                не писать ничего, тогда будут использованы
                ярлыки по умолчанию */
                'name' => 'Категории услуг',
                'singular_name' => 'Категория услуги',
                'search_items' => 'Найти категорию',
                'popular_items' => 'Популярные категории',
                'all_items' => 'Все категории',
                'parent_item' => null,
                'parent_item_colon' => null,
                'edit_item' => 'Редактировать категорию услуги',
                'update_item' => 'Обновить категории услуг',
                'add_new_item' => 'Добавить новую категорию',
                'new_item_name' => 'Название новой категории услуг',
                'add_or_remove_items' => 'Добавить или удалить категорию услуги',
                'choose_from_most_used' => 'Выбрать из наиболее часто используемых категорий услуг',
                'not_found' => 'Категории услуг не найдены.',
                'not_found_in_trash' => 'В корзине нет категорий услуг.',
                'menu_name' => 'Категории'
            ) ,
            'public' => true,
            /* каждый может использовать таксономию, либо
             только администраторы, по умолчанию - true */
            'show_in_nav_menus' => true,
            /* добавить на страницу создания меню */
            'show_ui' => true,
            /* добавить интерфейс создания и редактирования */
            'show_tagcloud' => true,
            /* нужно ли разрешить облако тегов для этой таксономии */
            'update_count_callback' => '_update_post_term_count',
            /* callback-функция для обновления счетчика $object_type */
            'query_var' => true,
            /* разрешено ли использование query_var, также можно
            указать строку, которая будет использоваться в качестве
            него, по умолчанию - имя таксономии */
            'rewrite' => array(
                /* настройки URL пермалинков */
                'slug' => 'service-category', // ярлык
                'hierarchical' => false
                // разрешить вложенность
                
            ) ,
        ));
    }

    // Тексты уведомлений
    function post_type_service_messages($messages)
    {
        global $post, $post_ID;

        $messages['service'] = array( // service - название созданного нами типа записей
            0 => '', // Данный индекс не используется.
            1 => sprintf('Услуга обновлена. <a href="%s">Просмотр</a>', esc_url(get_permalink($post_ID))) ,
            2 => 'Параметр обновлён.',
            3 => 'Параметр удалён.',
            4 => 'Услуга обновлена',
            5 => isset($_GET['revision']) ? sprintf('Услуга восстановлена из редакции: %s', wp_post_revision_title((int)$_GET['revision'], false)) : false,
            6 => sprintf('Услуга опубликована на сайте. <a href="%s">Просмотр</a>', esc_url(get_permalink($post_ID))) ,
            7 => 'Услуга сохранена.',
            8 => sprintf('Отправлено на проверку. <a target="_blank" href="%s">Просмотр</a>', esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))) ,
            9 => sprintf('Запланировано на публикацию: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Просмотр</a>', date_i18n(__('M j, Y @ G:i') , strtotime($post->post_date)) , esc_url(get_permalink($post_ID))) ,
            10 => sprintf('Черновик обновлён. <a target="_blank" href="%s">Просмотр</a>', esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))) ,
        );

        return $messages;
    }
    // Тексты уведомлений
    function post_type_request_messages($messages)
    {
        global $post, $post_ID;

        $messages['service'] = array( // service - название созданного нами типа записей
            0 => '', // Данный индекс не используется.
            1 => sprintf('Заявка обновлена. <a href="%s">Просмотр</a>', esc_url(get_permalink($post_ID))) ,
            2 => 'Параметр обновлён.',
            3 => 'Параметр удалён.',
            4 => 'Заявка обновлена',
            5 => isset($_GET['revision']) ? sprintf('Заявка восстановлена из редакции: %s', wp_post_revision_title((int)$_GET['revision'], false)) : false,
            6 => sprintf('Заявка опубликована на сайте. <a href="%s">Просмотр</a>', esc_url(get_permalink($post_ID))) ,
            7 => 'Заявка сохранена.',
            8 => sprintf('Отправлено на проверку. <a target="_blank" href="%s">Просмотр</a>', esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))) ,
            9 => sprintf('Запланировано на публикацию: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Просмотр</a>', date_i18n(__('M j, Y @ G:i') , strtotime($post->post_date)) , esc_url(get_permalink($post_ID))) ,
            10 => sprintf('Черновик обновлён. <a target="_blank" href="%s">Просмотр</a>', esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))) ,
        );

        return $messages;
    }

    function widget_init()
    {
        register_widget('WtServiceWidget_');
    }

    function shortcode_service_table_action($param, $content)
    {

        if ($_SERVER["CONTENT_TYPE"] == 'application/json') return;

        $args = array(
            'post_type' => 'service',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'numberposts' => - 1 // Количество выводимых позиций
            
        );

        if (!empty($param['filter_service_category'])) $args['service_category'] = $param['filter_service_category'];

        $services = get_posts($args);

        if (count($services) == 0)
        {
            echo 'Услуги отсутствуют';
            return;
        }

        echo "<div style='display: flex;flex-direction: row;flex-wrap: wrap;'><div style='max-width: 520px;min-width: 300px;width: 520px; margin: 0 auto;'><h4>Услуги</h4>";
        foreach ($services as $service)
        {
            $price = number_format(get_post_meta($service->ID, 'price', true) , 0, ',', ' ');
            $max = get_post_meta($service->ID, 'max', true);

            echo "<div style='max-width: 520px; overflow: hidden; text-overflow: ellipsis; padding: 4px 10px 4px 8px; margin:4px 0px 4px; display:flex; width:100%; border: 2px solid var(--sp-border-light); background: #fbfbfb;'>
			<div style='margin-top:4px; margin-left:4px; text-align: left; margin-right: 8px;'>
			<p name='name' style='margin-bottom: 0px;'>$service->post_title</p>
			<small style = 'display: flex;'>$service->post_content</small>
			</div>
			<div style='white-space: nowrap; margin-right: 10px; text-align: right; margin-left: auto;margin-top: 4px; '>
			<p style='margin-bottom: 0px;'><span name='price' id='price$service->ID'>$price</span> ₽</p>
			<small style='display:none;'><span style='margin-right:2px; margin-left: auto;' name='itog' id='itog$service->ID'>0</span> ₽</small>
			</div>
			<div style='display:flex; height: 50px;'>
			<button id='button$service->ID' style='width: 80px; margin:auto;padding-left: 10px; padding-right: 10px;' onclick='buttonclicked(\"$service->ID\")'>Добавить</button>
			<input autocomplete='off' style='display:none; width: 100px; margin:auto; padding-left: 8px;padding-right: 4px;width: 80px;' name='kolvo' id='kolvo$service->ID' type='number' step='1' min='0' max='$max' placeholder='0' onchange='calculate(\"$service->ID\")'>
			</div>
			</div>";

        }
        echo '</div><div style="max-width: 520px;min-width: 300px;width: 520px; margin: 0 auto;">';
        if (isset($param['contact-form-7_id']))
        {
            echo '<h4>Заказ</h4>';
        }
        else echo '<h4> </h4>';
        echo '<h5>Итого: <span id="all_itog" style="margin-right:2px;">0</span>₽</h5>';
        if (isset($param['contact-form-7_id']))
        {
            echo do_shortcode('[contact-form-7 id="' . $param['contact-form-7_id'] . '" title="Заказы услуг"]');
        }
        echo '</div></div><script>';

        if (isset($param['contact-form-7_input_name']))
        {
            echo 'var message = "' . $param['contact-form-7_input_name'] . '";';
        }
        echo '
			var formatter = new Intl.NumberFormat("ru");
			var itogs = document.getElementsByName("itog");
			var kolvos = document.getElementsByName("kolvo");
			var names = document.getElementsByName("name");
			var all_itog = document.getElementById("all_itog");
			var all_kolvo = document.getElementById("all_kolvo");
		
		function buttonclicked(from) {
			document.getElementById("kolvo"+from).value= 1;
			document.getElementById("kolvo"+from).style.display = "";
			document.getElementById("button"+from).style.display = "none";
			document.getElementById("kolvo"+from).focus();
			calculate(from);
		}
		function calculate(from) {

			var summ =	0;
			var summ_kolvo = 0;
			
			
			document.getElementById("itog"+from).innerText = formatter.format((Number(document.getElementById("kolvo"+from).value.replace(/\D+/g,"")) * Number(document.getElementById("price"+from).innerText.replace(/\D+/g,""))));
			all_itog.innerText = document.getElementById("itog"+from).innerText;
			
			
			document.getElementsByName(message)[0].value="";
			for(let i = 0;i<itogs.length;i++)
			 {
				summ_kolvo += (Number(kolvos[i].value.replace(/\D+/g,"")));
				summ += (Number(itogs[i].innerText.replace(/\D+/g,"")));
				
				
				if(Number(kolvos[i].value.replace(/\D+/g,""))>0)
				{
					document.getElementsByName(message)[0].value += names[i].innerText + " ("+kolvos[i].value+") "+ itogs[i].innerText + " руб.\r\n";
				}
			 }
			if(summ>0)
			{
				document.getElementsByName("your-message")[0].value += "Итого: (" +summ_kolvo+") "+ formatter.format(summ) + " руб.";
			}
			all_itog.innerText = formatter.format(summ);
			//all_kolvo.innerText = summ_kolvo;
			
			
			
			if(document.getElementById("kolvo"+from).value < 1)
			{
			document.getElementById("kolvo"+from).value = null;
			document.getElementById("kolvo"+from).style.display = "none";
			document.getElementById("button"+from).style.display = "";
			document.getElementById("itog"+from).parentElement.style.display="none";
			}
			else{
			document.getElementById("itog"+from).parentElement.style.display="flex";
			}
		}


		</script>';

        return;
    }
}

$wp_service_manager = new WtServiceManager_();

?>
