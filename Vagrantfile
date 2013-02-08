# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|
  config.vm.box = "ubuntu_server_12_10_amd64"
  config.vm.network :hostonly, "192.168.242.2"
  config.vm.provision :shell, :path => "deploy/deploy.sh"
end
