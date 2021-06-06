Специально для ntk-nk.ru
Author: Александр Голубин
Плагин поддерживает различные категории услуг, описание к ним, максимально возможное количество и подсчет выбранного количества услуг пользователем.
Для этого были использованы дополнительные плагины: Contact Form 7 и wp import export lite.

=====ШОРТКОД=====

filter_service_category — Фильтрация по категории. Значением может выступать название или slug категории.
contact-form-7_id — ID контактной формы, если отсутствует то не отображается.
contact-form-7_input_name — (например your-message) обязателен для отправки заказа пользователя.

Пример:
[wp_service_table filter_service_category="1с" contact-form-7_id="143" contact-form-7_input_name="your-message"]


=====КОНТАКТНАЯ ФОРМА=====

Для отправки заказа пользователя необходимо скрыть textarea и label к ней в настройках шорткода контактной формы с помощью hidden.

<label hidden='hidden'> Your message (optional)
    [textarea your-message] </label>


=====ИМПОРТ=====

Для импорта/экспорта рекомендуется использовать:
https://ru.wordpress.org/plugins/wp-import-export-lite/

Видео инструкция: https://youtu.be/H2v-9qzD298

Для указания цены и максимального количества следует использовать price и max переменные соответственно.
Так же услуги можно добавлять и изменять в админ панели с помощью вкладки "услуги".


Forked from: WT Service Manager