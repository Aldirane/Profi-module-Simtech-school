REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    41,
    'en',
    'First department',
    '<p>Бизнес-задача</p>\r\n<ul><li>Создать интерфейс управления списком отделов на базе CS-Cart последней версии;</li>
    <li>Создать страницы со списком отделов и карточкой отдела для повышения лояльности покупателей сайта;</li></ul>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    42,
    'en',
    'Second department',
    '<p>Техническое задание</p>\r\n<ul><li>В рамках задания необходимо создать новую сущность -&nbsp;<strong class=\"redactor-inline-converted\">Отделы</strong></li><li>В панели администратора CS-Cart в меню Покупатели (англ. Users) необходимо создать подраздел&nbsp;<strong>Отделы</strong></li></ul>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    43,
    'en',
    'Third department',
    '<p>При переходе в меню&nbsp;<strong>Покупатели &gt; Отделы</strong>&nbsp;должен отображаться интерфейс управления отделами. Основные функции:</p><ul><li>Возможность удалить отдел</li></ul><ul><li>Возможность перейти на страницу создания отдела</li></ul><ul><li>Возможность перейти на страницу редактирования отдела</li></ul><ul><li>Возможность поменять статус отдела (см. раздел Поля отдела)</li></ul><ul><li>Возможность поиска по статусу отдела и названию отдела</li></ul><ul><li>Возможность сортировки по полям таблицы (название, статус)</li></ul><ul><li>Паджинация</li></ul>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    44,
    'en',
    'Fourth department',
    '<p>Список отделов визуально должен быть оформлен в общей стилистики панели администратора CS-Cart и содержать минимум три поля (логотип, название, статус). В качестве аналога рекомендуется использовать Коллекции товаров из видеоуроков</p>'),(44,'ru','Fourth department','<p>Список отделов визуально должен быть оформлен в общей стилистики панели администратора CS-Cart и содержать минимум три поля (логотип, название, статус). В качестве аналога рекомендуется использовать Коллекции товаров из видеоуроков</p>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    46,
    'en',
    'Sixth department',
    '<p>При редактировании отдела должны быть доступны поля аналогичные созданию нового отдела. Дополнительно необходимо вывести поле для чтения - дата создания отдела. Дата создания отдела должна автоматически сохранять при создании отдела.</p>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    47,
    'en',
    'Seventh department',
    '<p>В витрине магазина должна быть доступна новая страница списка отделов (dispatch необходимо придумать самому). Для списка отделов рекомендуется сделать свою копию списка товаров, см. скрин (взято с категории Electronics на&nbsp;<a href=\"https://demo.cs-cart.com/\" target=\"_blank\">https://demo.cs-cart.com</a>)</p>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    49,
    'en',
    'Ninth department',
    '<p>На списке отделов должна также присутствовать паджинация, а также отображаться только отделы со статусом&nbsp;<strong>Вкл</strong>.</p>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    50,
    'en',
    'Tenth department',
    '<p>Карточку отдела в витрина рекомендуется сделать по аналогии со страницей бренда с допущением, что список товаров на бренде = список сотрудников отдела.</p>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    51,
    'en',
    'Eleventh department',
    '<p>Нюансы</p>\r\n<ul><li>При написании кода для панели администратора и витрины предполагается использование стандартных классов и стилей, т.е. в рамках сданных работ не предполагается CSS-код</li><li>При редактировании/создании/удалении tpl (Smarty), css, js-файлов рекомендуется чистить кеш файлов CS-Cart, см. способ&nbsp;<a href=\"https://www.cs-cart.ru/docs/4.1.x/developer/howto/addon/cache.html\">https://www.cs-cart.ru/docs/4....</a></li><li>Авто-тест рекомендуется добавить в папку&nbsp;<strong>var/tools/autotests</strong></li></ul>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    52,
    'en',
    'Twelfth department',
    '<p>Формат сдачи результата</p>\r\n<p>Для корректной сдачи необходимо прикрепить ссылку на pull-запрос с созданным кодом и не менее 5 коммитов</p>\r\n<p>Как правильно создать репозиторий</p>\r\n<ul><li>Зарегистрироваться на github.</li><li>Создать новый приватный репозиторий и склонировать локально.</li><li>Расшарить новый репозиторий на следующие ники преподавателей&nbsp;<strong class=\"redactor-inline-converted\">DSudakov</strong>,&nbsp;<strong class=\"redactor-inline-converted\">bimib</strong>,<strong class=\"redactor-inline-converted\">&nbsp;tamrazz,&nbsp;NickSvetlichnyy</strong></li><li>В ветку&nbsp;<strong class=\"redactor-inline-converted\">master (main)</strong>&nbsp;добавить файл .gitignore из&nbsp;<a href=\"https://github.com/Kolyn236/simtechdev-lesson.git\" target=\"_blank\">репозитория</a>, закомитить.</li><li>В ветку&nbsp;<strong>master (main)</strong>&nbsp;добавить код коробочной версии CS-Cart и закомитить.</li><li>Далее необходимо создать рабочую ветку (например, departments, development, advanced-lesson) и вести разработку в ней.</li></ul>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    53,
    'en',
    'Thirteenth department',
    '<p>Как правильно создать pull-запрос</p>\r\n<ul><li>Его можно создать сразу же после выполнения инструкции “Как правильно создать репозиторий”, но ни в коем случае не выполнять мерж (merge).</li><li>Pull-запрос создается из рабочей ветки в ветку&nbsp;<strong>master (main)</strong>. Предполагается, что в нем будет весь уникальный код, который вы делаете по заданию.</li><li>Обязательно добавляем&nbsp;<a href=\"https://readme.md/\" target=\"_blank\">README.md</a>&nbsp;файл с инструкциями.</li></ul>'),(53,'ru','Thirteenth department','<p>Как правильно создать pull-запрос</p>\r\n<ul><li>Его можно создать сразу же после выполнения инструкции “Как правильно создать репозиторий”, но ни в коем случае не выполнять мерж (merge).</li><li>Pull-запрос создается из рабочей ветки в ветку&nbsp;<strong>master (main)</strong>. Предполагается, что в нем будет весь уникальный код, который вы делаете по заданию.</li><li>Обязательно добавляем&nbsp;<a href=\"https://readme.md/\" target=\"_blank\">README.md</a>&nbsp;файл с инструкциями.</li></ul>'),(54,'en','Fourteenth department','<p>Какие инструкции требуются в README.md</p>\r\n<ul><li>Как развернуть ваше приложение на новый магазин CS-Cart (рекомендуется создать вторую копию CS-Cart локально и развернуть ваше приложение там, соответственно описав все шаги);</li><li>Как протестировать ваше приложение (включая тест-кейсы)</li><li>Как запустить авто-тест?</li></ul>'),(54,'ru','Fourteenth department','<p>Какие инструкции требуются в README.md</p>\r\n<ul><li>Как развернуть ваше приложение на новый магазин CS-Cart (рекомендуется создать вторую копию CS-Cart локально и развернуть ваше приложение там, соответственно описав все шаги);</li><li>Как протестировать ваше приложение (включая тест-кейсы)</li><li>Как запустить авто-тест?</li></ul>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (
    56,
    'en',
    'Sixteenth department',
    '<p><span class=\"style-scope yt-formatted-string\">Смотри хорошее кино на viju с промокодом KRITIKA - </span><a href=\"https://www.youtube.com/redirect?event=video_description&redir_token=QUFFLUhqbHM2U3hwOUwwVkFJZlZNN2hQSXdyeGdaSFN5d3xBQ3Jtc0tramdzYjlOd2dwZElrVlUyeGthczNHbGRVdVpESGh2emdaS0xTdGdjNDdreVNkT25qSW5Fc3NWTDhhdi1Pa0k0dDdUSUNxTGkzUngzMXVqX242OTIxNENGTkZNbS1JUXVwS09QNGdsQ2NSTlZaTmxwdw&q=https%3A%2F%2Fclck.ru%2F335qFr&v=7PMUM2eUQ7Y\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" rel=\"nofollow\" target=\"_blank\" dir=\"auto\">https://clck.ru/335qFr</a><span class=\"style-scope yt-formatted-string\"> Попробовать «Мой Канал»: </span><a href=\"https://www.youtube.com/redirect?event=video_description&redir_token=QUFFLUhqa2UwM0JpZ05xYlVyWFhib2E2dkZnU05OZ0Q2QXxBQ3Jtc0trOGp1WEt1ZGQ3eG93T2R4MVlTT1RTRkhhODBEclZwTjI4cGR6WTVQclhaTkhWaGdRVTRrYlMyOTBGRE1hOHJkQUJhZGVhZ3EzemlLblJTeHJ5Vjl3Qy1UWU5kWjVRSE4tRmhvRTF2ZEYwRGtiRjc5VQ&q=https%3A%2F%2Fclck.ru%2F335qGj&v=7PMUM2eUQ7Y\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" rel=\"nofollow\" target=\"_blank\" dir=\"auto\">https://clck.ru/335qGj</a></p><p><span class=\"style-scope yt-formatted-string\"></span></p>\r\n<p>Кинопаблик ВК - <a href=\"https://www.youtube.com/redirect?event=video_description&redir_token=QUFFLUhqbkhNcnc2NFJDa1Y0X1NodXo5WmcwUXhSTF96Z3xBQ3Jtc0tuRnl3NDlueHRlZjVpTTAzTzhvZ3FCRTVVeFlILVFxQ1JQY2RCRnUxVm10LWlpWS00RmFId09yY0hLckc1Q0ZXRXIyblNqNjh0S2p4dmZqZERjNU1fUUQ2amN4UGEyOGVxbXBjMHlvTnBRaEtZZDlMNA&q=https%3A%2F%2Fvk.com%2Fkinokritika_pro&v=7PMUM2eUQ7Y\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" rel=\"nofollow\" target=\"_blank\" dir=\"auto\">https://vk.com/kinokritika_pro</a><span class=\"style-scope yt-formatted-string\"><br>Мой Киноманский клуб:<br>На Boosty, для тех, кто из России - </span><a href=\"https://www.youtube.com/redirect?event=video_description&redir_token=QUFFLUhqbHdsY21UeDN2a2pBMDVrQzE1UkR2MlJPcDU3UXxBQ3Jtc0ttRm1vYjlETjk3aXZRM1FCOUdTVEdrUEFiZmJJaksyVnFGajI0TzlnY1BLdGRRRElQcENoSUk0R1FLTXNTeDl2XzVlLWZPNk9nU0J1LU9FQVJrV0NGX3BzSDRnSXVKQnU0ZDlodGlaVEFlYkxuOHZydw&q=https%3A%2F%2Fboosty.to%2Fkinokritika&v=7PMUM2eUQ7Y\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" rel=\"nofollow\" target=\"_blank\" dir=\"auto\">https://boosty.to/kinokritika</a><span class=\"style-scope yt-formatted-string\"><br>На Patreon, для тех, кто НЕ из России - </span><a href=\"https://www.youtube.com/redirect?event=video_description&redir_token=QUFFLUhqbkZRdTU3akc1TWo0bmE1dmdjY2oxaWNkMlZ3d3xBQ3Jtc0tsSVpuOU1vR1d5UVRpUjUwMzlVVndaVG42dTNTakxBVUVNNDhSem0tUU1OdjBrWmdWQWVFNEdaZ3RoeFlKYVJFeDhOSDlPRGpSRFBhZloyOFltSDVTT1A5WDc0OVQ0TTRkTnJwb2VZS3gyRGpBTXQzWQ&q=https%3A%2F%2Fwww.patreon.com%2Fkinokritika&v=7PMUM2eUQ7Y\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" rel=\"nofollow\" target=\"_blank\" dir=\"auto\">https://www.patreon.com/kinokritika</a><span class=\"style-scope yt-formatted-string\"></span></p>\r\n<p>Содержание выпуска:<br><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=0s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">00:00</a><span class=\"style-scope yt-formatted-string\"> Марвел закончился </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=180s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">03:00</a><span class=\"style-scope yt-formatted-string\"> А как оно было? </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=240s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">04:00</a><span class=\"style-scope yt-formatted-string\"> За что любили? </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=313s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">05:13</a><span class=\"style-scope yt-formatted-string\"> Переломный момент </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=350s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">05:50</a><span class=\"style-scope yt-formatted-string\"> Эмоций больше нет </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=445s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">07:25</a><span class=\"style-scope yt-formatted-string\"> Зато есть семья </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=580s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">09:40</a><span class=\"style-scope yt-formatted-string\"> Пропаганда бесит </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=790s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">13:10</a><span class=\"style-scope yt-formatted-string\"> Понабрали по объявению </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=850s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">14:10</a><span class=\"style-scope yt-formatted-string\"> Критики продались </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=920s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">15:20</a><span class=\"style-scope yt-formatted-string\"> Сюжета больше нет </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=980s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">16:20</a><span class=\"style-scope yt-formatted-string\"> Тайна Мультивселенной </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=1080s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">18:00</a><span class=\"style-scope yt-formatted-string\"> Культовые режиссеры о Марвел </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=1150s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">19:10</a><span class=\"style-scope yt-formatted-string\"> Массовая инфантилизация </span><a href=\"https://www.youtube.com/watch?v=7PMUM2eUQ7Y&t=1220s\" class=\"yt-simple-endpoint style-scope yt-formatted-string\" spellcheck=\"false\" dir=\"auto\">20:20</a><span class=\"style-scope yt-formatted-string\"> Культурная смерть</span></p>\r\n<p>Смерть киновселенной Марвел (итоги 2022 и 4 фазы)<br>Кинокритика Илья Бунин</p><p></p>'
);
REPLACE INTO ?:department_descriptions (`department_id`, `lang_code`, `department`, `description`) VALUES (57,'en','New department','');