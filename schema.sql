-- objects, types

CREATE TABLE pages (
    id          CHAR(40)        NOT NULL UNIQUE,
    segment     VARCHAR(255)    NOT NULL,
    timestamp   INT             NOT NULL,
    parent      CHAR(40),       -- parent page to determine page heirachy. Update heirachy table on CRuD, with admin function to recompile.
    layout      VARCHAR(255),   -- the view to use from themes/pages.
    blacklist   BIT(1),         -- 1 means black list, 0 means black list, null means public.
);

CREATE TABLE page_revisions (
    id          CHAR(40)        NOT NULL UNIQUE,
    page        CHAR(40)        NOT NULL,
    revision    INT             NOT NULL,
    timestamp   INT             NOT NULL,
    author      CHAR(40)        NOT NULL,
    content     TEXT,
);

-- Users
    -- id, username, hash, group.

-- Groups

-- White listing or blacklisting pages from groups.
CREATE TABLE group_pages (
    id          CHAR(40)        NOT NULL UNIQUE,
    group       CHAR(40)        NOT NULL,
    page        CHAR(40)        NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE heirachy (
    id          CHAR(40)        NOT NULL UNIQUE,
    route       TEXT            NOT NULL UNIQUE,
    page_id     CHAR(40)        NOT NULL,
    PRIMARY KEY (id)
);