truncate admin_login_logs;
truncate admin_logs;
truncate agent_day_report;
truncate error_logs;
truncate notify_downs;
truncate notify_ups;
truncate operation_logs;
truncate orders;
truncate payout_notify_downs;
truncate payout_notify_ups;
truncate payouts;
truncate report_days;
truncate report_months;
truncate report_real_times;
truncate report_years;

delete from merchants where merchant_name != '安信09';
delete from merchant_apps where merchant_id not in (select id from merchants);

alter table channel_payments auto_increment = 168800;

-- truncate menus
-- truncate merchant_accounts
-- truncate merchant_apps
-- truncate merchant_cards
-- truncate merchant_changes
-- truncate merchant_channels
-- truncate merchant_rate
-- truncate merchant_settles
-- truncate merchants
-- truncate permission_ips
-- truncate provinces
-- truncate agents
-- truncate banks
-- truncate card_records
-- truncate cards
-- truncate channel_configs
-- truncate channel_down_streams
-- truncate channel_payments
-- truncate channel_products
-- truncate channel_up_streams
-- truncate channels
-- truncate cities
-- truncate configs
-- truncate districts
-- truncate egame
-- truncate admin_roles
-- truncate admins
