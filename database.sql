CREATE SEQUENCE address_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 START 3753554 CACHE 1;

CREATE TABLE "public"."address" (
    "id" integer DEFAULT nextval('address_id_seq') NOT NULL,
    "street" character varying(255) NOT NULL,
    "city" character varying(255) NOT NULL,
    "postcode" character varying(20) NOT NULL,
    "post_office" character varying(255) NOT NULL,
    "street_search" tsvector NOT NULL,
    "city_search" tsvector NOT NULL,
    "postcode_search" tsvector NOT NULL,
    "post_office_search" tsvector NOT NULL,
    CONSTRAINT "address_city_street_postcode_post_office" UNIQUE ("city", "street", "postcode", "post_office"),
    CONSTRAINT "address_id" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "address_city" ON "public"."address" USING btree ("city");

CREATE INDEX "address_city_search" ON "public"."address" USING gin ("city_search");

CREATE INDEX "address_post_office" ON "public"."address" USING btree ("post_office");

CREATE INDEX "address_post_ofiice_search" ON "public"."address" USING gin ("post_office_search");

CREATE INDEX "address_postcode" ON "public"."address" USING btree ("postcode");

CREATE INDEX "address_postcode_search" ON "public"."address" USING gin ("postcode_search");

CREATE INDEX "address_street" ON "public"."address" USING btree ("street");

CREATE INDEX "address_street_search" ON "public"."address" USING gin ("street_search");