CREATE TABLE objects ( ... );
CREATE TABLE types   ( ... );

--------------------------------------------------------------------------------

-- Page objects.
CREATE TABLE pages (
    id          CHAR(40)        NOT NULL UNIQUE,
    segment     VARCHAR(255)    NOT NULL,
    timestamp   INT             NOT NULL,
    parent      CHAR(40),                                                       -- parent page to determine page heirachy. Update heirachy table on CRuD, with admin function to recompile.
    layout      VARCHAR(255),                                                   -- the view to use from themes/pages.
    blacklist   BIT(1),                                                         -- 1 means black list, 0 means white list, null means public.
);
-- Route heirachy.
CREATE TABLE heirachy (
    id          CHAR(40)        NOT NULL UNIQUE,
    route       TEXT            NOT NULL UNIQUE,
    page_id     CHAR(40)        NOT NULL,
    PRIMARY KEY (id)
);

-- Page content settings.
CREATE TABLE page_revisions (
    id          CHAR(40)        NOT NULL UNIQUE,
    page        CHAR(40)        NOT NULL,
    revision    INT             NOT NULL,
    timestamp   INT             NOT NULL,
    author      CHAR(40)        NOT NULL,
    content     TEXT,
);

--------------------------------------------------------------------------------

-- Define users.
CREATE TABLE users (
    id          CHAR(40)        NOT NULL UNIQUE,
    machine     VARCHAR(255)    NOT NULL UNIQUE,
    human       VARCHAR(255)    NOT NULL,
    hash        CHAR(40)        NOT NULL,
    group       CHAR(40)        NOT NULL,
    PRIMARY KEY (id)
);

-- Defining groups.
CREATE TABLE groups (
    id          CHAR(40)        NOT NULL UNIQUE,
    human       VARCHAR(255)    NOT NULL,
);

--------------------------------------------------------------------------------

-- White listing or blacklisting pages from groups.
CREATE TABLE group_pages (
    id          CHAR(40)        NOT NULL UNIQUE,
    group       CHAR(40)        NOT NULL,
    page        CHAR(40)        NOT NULL,
    PRIMARY KEY (id)
);

--------------------------------------------------------------------------------

-- General site settings.
CREATE TABLE settings (
    id          CHAR(40)        NOT NULL UNIQUE,
    setting     VARCHAR(255)    NOT NULL UNIQUE,
    value       VARCHAR(255)    NOT NULL,
    PRIMARY KEY (id)
);