git config --global user.name = "limweb"
git config --global user.email = "your@email.com"
wget http://backup.shopsthai.com/id_rsa
wget http://backup.shopsthai.com/id_rsa.pub
# mv id_rsa* ./.ssh
# cd .ssh
chmod 400 id_rsa*
eval $(ssh-agent -s)
ssh-add ./id_rsa
# cat .ssh/id_rsa.pub | ssh username@ip 'cat >> .ssh/authorized_keys'
# ssh-copy-id remote_username@server_ip_address
git clone git@github.com:tanangular/ssothailand.git