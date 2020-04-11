#!/bin/bash

# 確保目前沒有 ngrok 在執行（僅限免費帳號）
killall ngrok 2> /dev/null
killall php 2> /dev/null

echo "執行 websocker server";
php server/bin/chat-server.php 2> /dev/null &
sleep 0.5

echo "執行 ngrok";
sh -c "ngrok http 8080 > /dev/null &"
sleep 0.5

echo "等待中...";
sleep 3

echo "變更 client/index.html";
sub=$(curl --silent --show-error http://127.0.0.1:4040/api/tunnels \
    | sed -nE 's/.*public_url":"https:..([^"\.]*).*/\1/p')
sed -E -i "s/[0-9a-f]{8}/$sub/" client/index.html
sleep 0.5

echo "準備更新"
now=$(date +"%Y/%m/%d_%H:%M:%S")
git add .
git commit --author "AutoCommit <noreply@localhost>" -m "Auto update ($now)"
git push
sleep 0.5

echo "已完成"