## CHANNEL

* "id": INT(11)
* "name:" VARCHAR(64)

INDEX: name

## PROGRAM

* "id": INT(11)
* "title": VARCHAR(128)
* "short_description": VARCHAR(128)
* "start_datetime": DATETIME
* "channel": INT(11), PK: channel.id
    * ON DELETE: RESTRICT
    * ON UPDATE: CASCADE
* "age_restriction": INT(11), PK: age_restriction.id
    * ON DELETE: RESTRICT
    * ON UPDATE: CASCADE

## AGE_RESTRICTION

* "id": INT(11)
* "name": VARCHAR(128), UNIQUE
* "limit": TINYINT, UNIQUE
* "icon": VARCHAR(128), NULLABLE