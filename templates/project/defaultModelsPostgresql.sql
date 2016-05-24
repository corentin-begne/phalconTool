-- Converted by db_converter
START TRANSACTION;
SET standard_conforming_strings=off;
SET escape_string_warning=off;
SET CONSTRAINTS ALL DEFERRED;

CREATE TABLE "GenderType" (
    "id" integer NOT NULL,
    "name" varchar(64) NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "GenderType" VALUES (1,'male'),(2,'female');
CREATE TABLE "LangType" (
    "id" integer NOT NULL,
    "name" varchar(4) NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "LangType" VALUES (1,'fr'),(2,'en');
CREATE TABLE "SocialType" (
    "id" integer NOT NULL,
    "name" varchar(64) NOT NULL,
    PRIMARY KEY ("id")
);

INSERT INTO "SocialType" VALUES (1,'googlePlus');
CREATE TABLE "User" (
    "id" integer NOT NULL,
    "name" varchar(128) NOT NULL,
    "email" varchar(128) NOT NULL,
    "gender_id" integer NOT NULL,
    "social_id" integer NOT NULL,
    "lang_id" integer NOT NULL,
    "created_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id"),
    UNIQUE ("email")
);

CREATE TABLE "UserSocial" (
    "id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "is_verified" int4 NOT NULL DEFAULT '0',
    "token" text NOT NULL,
    PRIMARY KEY ("user_id"),
    UNIQUE ("user_id")
);


-- Post-data save --
COMMIT;
START TRANSACTION;

-- Typecasts --
ALTER TABLE "UserSocial" ALTER COLUMN "is_verified" DROP DEFAULT, ALTER COLUMN "is_verified" TYPE boolean USING CAST("is_verified" as boolean);

-- Foreign keys --
ALTER TABLE "User" ADD CONSTRAINT "gtid" FOREIGN KEY ("gender_id") REFERENCES "GenderType" ("id") DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON "User" ("gender_id");
ALTER TABLE "User" ADD CONSTRAINT "ltid" FOREIGN KEY ("lang_id") REFERENCES "LangType" ("id") DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON "User" ("lang_id");
ALTER TABLE "User" ADD CONSTRAINT "stid" FOREIGN KEY ("social_id") REFERENCES "SocialType" ("id") DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON "User" ("social_id");
ALTER TABLE "UserSocial" ADD CONSTRAINT "uid" FOREIGN KEY ("user_id") REFERENCES "User" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED;
CREATE INDEX ON "UserSocial" ("user_id");

-- Sequences --
CREATE SEQUENCE GenderType_id_seq;
SELECT setval('GenderType_id_seq', max(id)) FROM "GenderType";
ALTER TABLE "GenderType" ALTER COLUMN "id" SET DEFAULT nextval('GenderType_id_seq');
CREATE SEQUENCE LangType_id_seq;
SELECT setval('LangType_id_seq', max(id)) FROM "LangType";
ALTER TABLE "LangType" ALTER COLUMN "id" SET DEFAULT nextval('LangType_id_seq');
CREATE SEQUENCE SocialType_id_seq;
SELECT setval('SocialType_id_seq', max(id)) FROM "SocialType";
ALTER TABLE "SocialType" ALTER COLUMN "id" SET DEFAULT nextval('SocialType_id_seq');
CREATE SEQUENCE User_id_seq;
SELECT setval('User_id_seq', max(id)) FROM "User";
ALTER TABLE "User" ALTER COLUMN "id" SET DEFAULT nextval('User_id_seq');
CREATE SEQUENCE UserSocial_id_seq;
SELECT setval('UserSocial_id_seq', max(id)) FROM "UserSocial";
ALTER TABLE "UserSocial" ALTER COLUMN "id" SET DEFAULT nextval('UserSocial_id_seq');

-- Full Text keys --

COMMIT;
