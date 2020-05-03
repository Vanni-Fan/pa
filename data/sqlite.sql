/*
 Navicat Premium Data Transfer

 Source Server         : PA：sqlite3
 Source Server Type    : SQLite
 Source Server Version : 3021000
 Source Schema         : main

 Target Server Type    : SQLite
 Target Server Version : 3021000
 File Encoding         : 65001

 Date: 03/05/2020 19:20:19
*/

PRAGMA foreign_keys = false;

-- ----------------------------
-- Table structure for configs
-- ----------------------------
DROP TABLE IF EXISTS "configs";
CREATE TABLE "configs" (
  "config_id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "type" TEXT,
  "menu_id" INTEGER,
  "is_action_name" integer DEFAULT 0,
  "name" TEXT,
  "var_name" TEXT,
  "var_default" TEXT,
  "var_type" TEXT,
  "options" TEXT,
  "options_type" TEXT,
  "is_enabled" integer DEFAULT 1,
  "remark" TEXT,
  "created_time" integer,
  "updated_time" integer,
  "created_user" integer,
  "updated_user" integer
);

-- ----------------------------
-- Table structure for logs
-- ----------------------------
DROP TABLE IF EXISTS "logs";
CREATE TABLE "logs" (
  "log_id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "menu_id" INTEGER,
  "user_id" integer,
  "name" TEXT,
  "url" TEXT,
  "request" TEXT,
  "server" TEXT,
  "created_time" text
);

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS "menus";
CREATE TABLE "menus" (
  "menu_id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" TEXT NOT NULL,
  "is_displayable" integer,
  "url_suffix" TEXT,
  "modules" TEXT DEFAULT '*',
  "router" TEXT,
  "params" TEXT,
  "icon" TEXT,
  "parent_id" INTEGER,
  "index" integer DEFAULT 0,
  "is_enabled" integer,
  "corner_mark" TEXT,
  "remark" TEXT,
  "created_time" integer,
  "updated_time" integer,
  "created_user" integer,
  "updated_user" integer
);

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS "permissions";
CREATE TABLE "permissions" (
  "permission_id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "role_id" INTEGER,
  "type" TEXT,
  "menu_id" INTEGER,
  "config_id" INTEGER,
  "value" TEXT,
  "created_time" integer,
  "updated_time" integer,
  "created_user" integer,
  "updated_user" integer
);

-- ----------------------------
-- Table structure for plugins
-- ----------------------------
DROP TABLE IF EXISTS "plugins";
CREATE TABLE "plugins" (
  "plugin_id" INTEGER NOT NULL PRIMARY KEY,
  "name" TEXT,
  "class_name" TEXT,
  "is_enabled" TEXT,
  "icon_url" TEXT,
  "images" TEXT,
  "description" TEXT,
  "permission" TEXT,
  "official_url" TEXT,
  "author" TEXT,
  "author_url" TEXT,
  "version" TEXT,
  "match_version" TEXT,
  "license" TEXT,
  "publish_date" TEXT,
  "created_time" integer,
  "updated_time" integer,
  "created_user" integer,
  "updated_user" integer
);

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS "roles";
CREATE TABLE "roles" (
  "role_id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" TEXT NOT NULL,
  "is_enabled" integer,
  "remark" TEXT,
  "created_time" integer,
  "updated_time" integer,
  "created_user" integer,
  "updated_user" integer
);

-- ----------------------------
-- Table structure for user_configs
-- ----------------------------
DROP TABLE IF EXISTS "user_configs";
CREATE TABLE "user_configs" (
  "id" INTEGER NOT NULL PRIMARY KEY,
  "config_id" integer,
  "user_id" INTEGER,
  "menu_id" INTEGER,
  "name" TEXT,
  "value" TEXT,
  "is_enabled" integer,
  "remark" TEXT,
  "created_time" integer,
  "updated_time" integer,
  "created_user" integer,
  "updated_user" integer
);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS "users";
CREATE TABLE "users" (
  "user_id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "role_id" INTEGER,
  "name" TEXT NOT NULL,
  "nickname" TEXT,
  "password" TEXT,
  "image" TEXT,
  "mobile" TEXT,
  "is_enabled" integer DEFAULT 1,
  "remark" TEXT,
  "created_time" integer,
  "updated_time" integer,
  "created_user" integer,
  "updated_user" integer
);

PRAGMA foreign_keys = true;
