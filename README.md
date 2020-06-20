# porthu-displayer-assignment

A PHP script that saves and displays movies and series from Port.hu - made for an assignment.

## Note

Port.hu does not possess a documented programmable interface, but a "hidden" API is used to list channels and their programs. This project utilized that API instead of parsing HTML.

## How to use

### Requirements

* PHP 7.4
* SQLite 3

### Install

Create an SQLite database called `porthu`.

Execute `./database/porthu.sql` on it - so that the initial data structure is created.

## Usage

### Scripts

Run `php ./scripts/getPrograms.php --date <arbitrary-date>` to add programs of the specified day to the database.

### Server

Run `php -S localhost:8000 -t ./public` in the project root to start using the server on the address `http://localhost:8000`.