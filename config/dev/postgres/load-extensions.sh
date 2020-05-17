psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<EOF
create extension if not exists "uuid-ossp";
select * FROM pg_extension;
EOF