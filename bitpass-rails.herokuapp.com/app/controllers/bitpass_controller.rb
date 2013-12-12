class BitpassController < ApplicationController

  skip_before_filter :verify_authenticity_token

  def show
    #bitpass:?sm=[source message]&cbk=[callback url]&dt=[display title]
    bitpass = Bitpass.new()
    @qrcode = bitpass.get_qrcode_data_url
    @message = bitpass.get_message
    @verify_string = bitpass.get_verify_string
    @callback_url = bitpass.get_callback_url
  end


  def callback
    #address=[bitcoin address]&signature=[sign message with private key]&message=[source message]
    #Todo: 增加过期时间判断
    bitpass = Bitpass.verify(params[:address],params[:signature],params[:message])
    if bitpass then
      msg = "1\tOK"
    else
      msg = "-1\tNot verified"
    end
    p msg
    render text: msg
  end

  def trylogin
    #return render text: 'Error: error message to subscribe' unless /^[A-Za-z0-9:]+$/.match(params[:bitpass_message])
    if bitpass = Bitpass.verified?(params[:message])
      msg = "1\t#{bitpass}"
    else
      msg = "-1\tnot verifyed"
    end
    render text: msg
  end

end