# Course PHP-2

[Lesson-1](#Lesson-1)

[Lesson-2](#Lesson-2)

[Lesson-3](#Lesson-3)

[Lesson-4](#lesson-4)

[Lesson-5](#lesson-5)

[Lesson-6](#lesson-6)

## Lesson-1

Начало курсового проекта

Нашим курсовым проектом будет API для коллективного блога типа «Хабрахабра». У нас не
будет никакого фронтенда — по HTTP приложение будет отдавать только JSON-ответы. В
приложении будут пользователи. Пользователи смогут писать статьи и комментарии.

**1. Создайте репозиторий с проектом на GitHub**.

**2. Создайте простые классы:**

- для пользователей: id, имя, фамилия;

- статей: id, id автора, заголовок, текст;

- комментариев: id, id автора, id статьи, текст.

**3. Инициализируйте в проекте composer и настройте автозагрузку PSR-4, код классов
положите в папку src.**

**4. Подключите к проекту пакет fakerphp/faker.**

**5. Создайте в корневой папке проекта файл cli.php — это будет точка входа в наше пока
что только консольное приложение.**

**6. Реализуете логику:**

- При запуске с аргументом user приложение создаёт объект пользователя с
именем и фамилией, сгенерированными библиотекой fakerphp/faker, и
печатает его строковое представление в консоль. Используете
предопределённую переменную $argv для получения аргументов командной
строки. Определите метод __toString в классе пользователя.

- Повторите это для статей (агрумент post) и комментариев (comment).

- Примеры работы приложения:

```sh
➜ blog git:(master) php cli.php user
Ivan Nikitin

➜ blog git:(master) php cli.php post
Quod ut earum incidunt quas aut. >>> Rerum similique est saepe architecto eum.
Et placeat totam sit.

➜ blog git:(master) php cli.php comm
```

## Lesson-2

**1. Создайте таблицы для хранения статей и комментариев в БД SQLite:**

- Таблица статей должна иметь колонки: UUID статьи, UUID автора статьи, заголовок,
текст.

- Таблица комментариев должна иметь колонки: UUID комментария, UUID статьи, UUID
автора комментария, текст.

**2. Обновите классы пользователей, статей и комментариев так, чтобы их идентификаторы имели
тип UUID.**

**3. Создайте контракты репозиториев статей и комментариев — PostsRepositoryInterface и
CommentsRepositoryInterface. Контракты должны иметь по два метода:**

- get — принимающий UUID и возвращающий объект статьи или комментария.
- save — принимающий объект статьи или комментария и ничего не возвращающий.

**4. Создайте SQLite-реализации этих контрактов.**

## Lesson-3

**1. Напишите тесты для SQLite-репозитория статей:**

- статья сохраняется в репозиторий;

- репозиторий находит статью по UUID;

- репозиторий бросает исключение, если статья не найдена.

**2. Напишите тесты для SQLite-репозитория комментариев:**

- комментарий сохраняется в репозиторий;

- репозиторий находит комментарий по UUID;

- репозиторий бросает исключение, если комментарий не найден.

**3. * Добейтесь стопроцентного покрытия кода классов Arguments, Users, Posts, Comments и
UUID**

## Lesson-4

**1. Реализуйте возможность добавления комментария к статье таким образом, чтобы запрос
вида:**

```sh
POST http://127.0.0.1:8000/posts/comment
{
"author_uuid": "<UUID>",
"post_uuid": "<UUID>",
"text": "<TEXT>",
}
```

**приводил к созданию комментария в SQLite-репозитории.**

**2. Напишите модульные тесты для класса CreatePost:**

- класс возвращает успешный ответ;

- класс возвращает ошибку, если запрос содержит UUID в неверном формате;

- класс возвращает ошибку, если пользователь не найден по этому UUID;

- класс возвращает ошибку, если запрос не содержит всех данных, необходимых для
создания статьи.

**3. Реализуйте возможность удаления статьи таким образом, чтобы запрос вида:**

```sh
DELETE http://127.0.0.1:8000/posts?uuid=<UUID>
```

**приводил к удалению статьи из репозитория.**

## Lesson-5

Добавьте функциональность реакции на статьи — лайки:

**1. Создайте класс, описывающий один лайк со свойствами:**

- UUID лайка;

- UUID статьи;

- UUID пользователя, поставившего лайк.

**2. Создайте таблицу в базе данных SQLite для хранения лайков.**

**3. Создайте контракт репозитория лайков с двумя методами:**

- save — сохранить лайк;

- getByPostUuid — получить лайки для этой статьи.

**4. Создайте SQLite-реализацию репозитория лайков.**

**5. Создайте HTTP-действие для добавления лайка:**

- действие должно принимать UUID статьи и UUID пользователя;

- приложение не должно позволять пользователю ставить больше одного лайка статье.

**6*. Добавьте аналогичную функциональность лайков для комментариев.**

## Lesson-6

**1. Добавьте логирование для всех SQLite-репозиториев:**

- для всех методов сохранения объектов, таких как save(User $user), логируйте
сообщение с уровнем INFO и содержащее UUID объекта;

- для всех методов получения объектов, таких как get(UUID $uuid), логируйте
сообщение с уровнем WARNING и содержащее UUID объекта, если объект не найден.

**2. Некоторые тесты могут быть сломаны из-за того, что SQLite-реализации репозиториев теперь
требуют экземпляр логгера в качестве зависимости. Для удовлетворения таких зависимостей
используйте тестовую реализацию логгера. Запустите модульные тесты и убедитесь, что все
они работают.**
