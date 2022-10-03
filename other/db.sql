CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  password varchar(255) NOT NULL
);

/* создать пустой enum для должностей */
CREATE TYPE positions AS ENUM ();

/* создать enum для должностей с двумя записями */
CREATE TYPE positions AS ENUM ('Электрик', 'Сантехник');

/* выбрать все должности */
SELECT unnest(enum_range(NULL::positions));

/* подсчет всех должностей */
SELECT COUNT(*) FROM unnest(enum_range(NULL::positions));

/* добавить запись в enum */
ALTER TYPE positions ADD VALUE 'Оператор';

/* удалить тип в enum - не работает */
DELETE FROM pg_enum WHERE enumtypid = (SELECT oid FROM pg_type WHERE typname = 'positions') AND enumlabel = 'Оператор';

/* или - тоже не работает*/
DELETE FROM pg_enum WHERE enumlabel = 'Оператор' AND enumtypid = ( SELECT oid FROM pg_type WHERE typname = 'positions');

/* переименовать тип в enum */
ALTER TYPE positions RENAME VALUE 'Оператор' TO 'Оператор котельной';

/* поиск в enum по имени. сделать поисковую строку в нижнем регистре с помощью php или js? */
SELECT * FROM pg_enum WHERE enumlabel LIKE '%оператор%' AND pg_enum.enumtypid = (SELECT oid FROM pg_type WHERE typname = 'positions');

/* вернуть все записи в enum */
SELECT * FROM pg_enum WHERE pg_enum.enumtypid = (SELECT oid FROM pg_type WHERE typname = 'positions');

/* удалить все должности*/
DROP TYPE positions;

CREATE TYPE statuses AS ENUM ('Назначено', 'В работе', 'Завершено', 'Отказ');

CREATE TABLE workers (
  worker_id INT GENERATED ALWAYS AS IDENTITY,
  fullname VARCHAR(255) NOT NULL, /*not unique*/
  current_positions positions[] NOT NULL,
  phone VARCHAR(20),
  email VARCHAR(64),  
  created timestamp DEFAULT current_timestamp,
  deleted BOOLEAN DEFAULT FALSE,
  PRIMARY KEY(worker_id)
);

/* проверка существования работника */
SELECT * FROM workers WHERE fullname = 'Дядя Вася' AND current_positions =  '{Сантехник, Электрик}' AND phone='+76666666666' AND email='nomail@mail.com';

/* вставка нового работника */
INSERT INTO workers(fullname, current_positions, phone, email) VALUES ('Дядя Вася', '{Сантехник, Электрик}', '+76666666666', 'nomail@mail.com');

/* редактирование работника */
UPDATE workers SET fullname = 'Дядя Вася', current_positions = '{Сантехник, Электрик, Разнорабочий}', phone = '+76666666666', email = 'nomail@mail.com' WHERE worker_id='1';

/* подсчет количества работников по должностям*/
SELECT COUNT(worker_id) FROM workers WHERE array['Сантехник', 'Электрик']::positions[] <@ "current_positions";

/* подсчет количества работников по должностям + имени*/
SELECT COUNT(worker_id) FROM workers WHERE fullname LIKE '%Вася%' AND array['Сантехник', 'Электрик']::positions[] <@ "current_positions";

/* поиск работников по должностям */
SELECT * FROM workers WHERE array['Сантехник', 'Электрик']::positions[] <@ "current_positions";

/* поиск работников по должностям + имени*/
SELECT * FROM workers WHERE fullname LIKE '%Вася%' AND array['Сантехник', 'Электрик']::positions[] <@ "current_positions";

CREATE TABLE issues (
  issue_id INT GENERATED ALWAYS AS IDENTITY,
  worker_id INT,
  status statuses NOT NULL DEFAULT 'Назначено',
  position positions NOT NULL,
  creat_time timestamp(0) without time zone NOT NULL DEFAULT (now()::timestamp(0) without time zone),
  mod_time timestamp(0) without time zone NOT NULL DEFAULT (current_timestamp AT TIME ZONE 'UTC'),
  issue_date VARCHAR(128) NOT NULL,
  place VARCHAR(1024) NOT NULL,
  issue VARCHAR(2048) NOT NULL,
  notes VARCHAR(2048),
  urgent BOOLEAN DEFAULT FALSE,
  deleted BOOLEAN DEFAULT FALSE,
  PRIMARY KEY(issue_id),
  CONSTRAINT fk_worker
    FOREIGN KEY(worker_id) 
      REFERENCES workers(worker_id)
);

INSERT INTO issues (worker_id, creat_time, mod_time, issue_date, place, issue, notes, urgent) VALUES ('id', 'Электрик', current_timestamp, current_timestamp, '8-21-2022', 'Подвал', 'Заменить лампочку', '', false);

UPDATE issues SET status='В процессе', mod_time=current_timestamp WHERE issue_id='1';

SELECT COUNT(issue_id) AS total FROM issues INNER JOIN workers ON issues.worker_id = workers.worker_id WHERE issues.deleted = false AND workers.fullname LIKE '%я%' AND issues.status = 'Назначено'::statuses;