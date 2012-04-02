<?php

$query =<<<EOF

CREATE TABLE {$table_prefix}attribute (
    id serial NOT NULL,
    ascii_id character varying(200),
    collection_id integer,
    attribute_name character varying(200),
    usage_notes character varying(2000),
    sort_order integer DEFAULT 999,
    in_basic_search boolean DEFAULT true,
    is_on_list_display boolean DEFAULT true,
    is_public boolean DEFAULT true,
    mapped_admin_att_id integer DEFAULT 0,
    is_repeatable integer DEFAULT 1,
    is_required integer DEFAULT 0,
    updated character varying(50),
    modifier_type character varying(200),
    modifier_defined_list text,
    p_collection_ascii_id character varying(100) DEFAULT 0,
    html_input_type character varying(50) DEFAULT 'text'::character varying
);

CREATE TABLE {$table_prefix}attribute_item_type (
    id serial NOT NULL,
    item_type_id integer NOT NULL,
    attribute_id integer NOT NULL
);

CREATE TABLE {$table_prefix}collection (
    id serial NOT NULL,
    ascii_id character varying(200),
    collection_name character varying(200),
    remote_media_host character varying(200),
    description character varying(2000),
    is_public boolean,
    created character varying(50),
    updated character varying(50),
    visibility character varying(50),
    admin_notes text,
    item_count integer DEFAULT 0
);

CREATE TABLE {$table_prefix}collection_manager (
    id serial NOT NULL,
    collection_ascii_id character varying(200),
    dase_user_eid character varying(20),
    auth_level character varying(20),
    expiration character varying(50),
    created_by_eid character varying(50),
    created character varying(50)
);

CREATE TABLE {$table_prefix}comment (
    id serial NOT NULL,
    text text,
    "type" character varying(10),
    item_id integer,
    p_collection_ascii_id character varying(100),
    p_serial_number character varying(100),
    updated character varying(100),
    updated_by_eid character varying(100)
);

CREATE TABLE {$table_prefix}dase_user (
    id serial NOT NULL,
    eid character varying(255),
    name character varying(200),
    has_access_exception boolean DEFAULT false,
    controls_status character varying(200),
    display character varying(20),
    max_items integer,
    current_collections character varying(2000),
    service_key_md5 character varying(200),
    created character varying(50) DEFAULT 0,
    updated character varying(50) DEFAULT 0,
);

CREATE TABLE {$table_prefix}defined_value (
    id serial NOT NULL,
    attribute_id integer,
    sort_order integer DEFAULT 999,
    value_text character varying(200)
);

CREATE TABLE {$table_prefix}item (
    id serial NOT NULL,
    serial_number character varying(100),
    collection_id integer,
    item_type_id integer DEFAULT 0,
    created character varying(50) DEFAULT 0,
    updated character varying(50) DEFAULT 0,
    comments_updated character varying(50) DEFAULT 0,
    comments_count integer,
    status character varying(50),
    p_collection_ascii_id character varying(100),
    collection_name character varying(200),
    item_type_ascii_id character varying(200),
    item_type_name character varying(200),
    p_remote_media_host character varying(200),
    created_by_eid character varying(50)
);

CREATE TABLE {$table_prefix}item_atom (
    id serial NOT NULL,
    unique_id character varying(201) NOT NULL,
    doc text,
    updated character varying(50)
);

CREATE TABLE {$table_prefix}item_json (
    id serial NOT NULL,
    unique_id character varying(201) NOT NULL,
    doc text,
    updated character varying(50)
);

CREATE TABLE {$table_prefix}item_type (
    id serial NOT NULL,
    collection_id integer DEFAULT 0 NOT NULL,
    name character varying(200),
    ascii_id character varying(200) NOT NULL,
    description character varying(2000)
);

CREATE TABLE {$table_prefix}media_file (
    id serial NOT NULL,
    item_id integer,
    filename character varying(2000),
    height integer,
    width integer,
    mime_type character varying(200),
    size character varying(20),
    p_serial_number character varying(100) DEFAULT 0,
    p_collection_ascii_id character varying(100) DEFAULT 0,
    file_size integer,
    updated character varying(50),
    md5 character varying(200)
);

CREATE TABLE {$table_prefix}tag_item (
    id serial NOT NULL,
    tag_id integer,
    item_id integer,
    annotation character varying(2000),
    sort_order integer,
    size character varying(200),
    p_serial_number character varying(200) DEFAULT 0,
    p_collection_ascii_id character varying(200) DEFAULT 0,
    updated character varying(50)
);

CREATE TABLE {$table_prefix}recent_view (
    id serial NOT NULL,
    url character varying(4000),
    title character varying(200),
    type character varying(20),
    count integer,
    dase_user_eid character varying(100),
    "timestamp" character varying(50)
);

CREATE TABLE {$table_prefix}tag (
    id serial NOT NULL,
    name character varying(200),
    description character varying(200),
    dase_user_id integer,
    is_public boolean DEFAULT false,
    background character varying(20) DEFAULT 'white'::character varying,
    ascii_id character varying(200),
    created character varying(50),
    updated character varying(50),
    "type" character varying(50) DEFAULT 'set'::character varying,
    eid character varying(50),
    visibility character varying(50),
    item_count integer DEFAULT 0
);

CREATE TABLE {$table_prefix}tag_category (
    id serial NOT NULL,
    tag_id integer,
    category_id integer,
    term character varying(200),
    label character varying(200),
    scheme character varying(200)
);

CREATE TABLE {$table_prefix}value (
    id serial NOT NULL,
    item_id integer,
    attribute_id integer,
    value_text text,
    url character varying(2000),
    modifier character varying(2000)
);

CREATE SEQUENCE {$table_prefix}tag_category_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}attribute_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}attribute_item_type_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}collection_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}collection_manager_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}comment_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}dase_user_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}defined_value_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}item_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}item_type_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}media_file_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}tag_item_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}recent_view_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}tag_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE {$table_prefix}value_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

EOF;


$query .= "
ALTER TABLE {$table_prefix}tag_category 
ALTER id SET DEFAULT nextval('public.{$table_prefix}tag_category_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}attribute 
ALTER id SET DEFAULT nextval('public.{$table_prefix}attribute_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}attribute_item_type 
ALTER id SET DEFAULT nextval('public.{$table_prefix}attribute_item_type_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}collection 
ALTER id SET DEFAULT nextval('public.{$table_prefix}collection_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}collection_manager 
ALTER id SET DEFAULT nextval('public.{$table_prefix}collection_manager_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}comment 
ALTER id SET DEFAULT nextval('public.{$table_prefix}comment_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}dase_user 
ALTER id SET DEFAULT nextval('public.{$table_prefix}dase_user_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}defined_value 
ALTER id SET DEFAULT nextval('public.{$table_prefix}defined_value_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}item 
ALTER id SET DEFAULT nextval('public.{$table_prefix}item_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}item_type 
ALTER id SET DEFAULT nextval('public.{$table_prefix}item_type_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}media_file 
ALTER id SET DEFAULT nextval('public.{$table_prefix}media_file_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}tag_item 
ALTER id SET DEFAULT nextval('public.{$table_prefix}tag_item_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}recent_view 
ALTER id SET DEFAULT nextval('public.{$table_prefix}recent_view_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}tag 
ALTER id SET DEFAULT nextval('public.{$table_prefix}tag_seq'::text);
";
$query .= "
ALTER TABLE {$table_prefix}value 
ALTER id SET DEFAULT nextval('public.{$table_prefix}value_seq'::text);
";
