BEGIN TRANSACTION;

DROP TABLE IF EXISTS "program";

CREATE TABLE IF NOT EXISTS "program" (
	"id"	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	"title"	TEXT NOT NULL,
	"short_description"	TEXT DEFAULT NULL,
	"start_datetime"	TEXT NOT NULL DEFAULT 'now',
	"channel"	INTEGER NOT NULL,
	"age_restriction"	INTEGER NOT NULL,
	FOREIGN KEY("age_restriction") REFERENCES "age_restriction"("id"),
	FOREIGN KEY("channel") REFERENCES "channel"("id")
);

DROP TABLE IF EXISTS "age_restriction";

CREATE TABLE IF NOT EXISTS "age_restriction" (
	"id"	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	"name"	TEXT NOT NULL UNIQUE,
	"limit"	INTEGER DEFAULT NULL UNIQUE,
	"icon"	TEXT DEFAULT NULL UNIQUE
);

DROP TABLE IF EXISTS "channel";

CREATE TABLE IF NOT EXISTS "channel" (
	"id"	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	"name"	TEXT NOT NULL UNIQUE
);
COMMIT;
