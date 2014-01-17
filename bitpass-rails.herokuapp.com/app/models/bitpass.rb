class Bitpass
  BITPASS_CONFIG = YAML::load_file(File.join(Rails.root, "config/bitpass.yml"))[Rails.env]
  def initialize
    super
    @callback_url = url_encode(BITPASS_CONFIG["callback_url"])
    display_title = url_encode(BITPASS_CONFIG["display_title"])
    @message = generate_message 
    @verify_string = "bitpass:?sm=#{@message}&cbk=#{@callback_url}&dt=#{display_title}"
  end

  def get_qrcode_data_url width=220,height=220
    #QREncoder.encode(@verify_string).png.resize(width, height).to_data_url
    RQRCode::QRCode.new(@verify_string,:level =>:l).as_png.resize(width, height).to_data_url
    #QREncoder.encode(@verify_string).png.resize(width, height).to_data_url
  end

  def get_message
    @message
  end

  def get_verify_string
    @verify_string
  end

  def get_callback_url
    URI.unescape(@callback_url)
  end

  #验证
  def self.verify address,signature,message
    #address=[bitcoin address]&signature=[sign message with private key]&message=[source message]
    p address = URI.unescape(address)
    p signature = URI.unescape(signature)
    p message = URI.unescape(message)
    if Bitcoin.verify_message(address,signature,message) && (bitpass = BitpassStorage.where(:source_message=>message).first) && bitpass.btc_address.blank?
      bitpass.signature_base64 = signature
      bitpass.btc_address = address
      bitpass.verify_time = Time.now
      bitpass.save()
    else
      false
    end
  end

  #是否已被验证
  def self.verified? message
    if (bitpass = BitpassStorage.where(:source_message=>message).first) && bitpass.btc_address
      bitpass.btc_address
    else
      false
    end
  end

  private
  # 生成新message
  def generate_message
    #bitpass:?sm=[source message]&cbk=[callback url]&dt=[display title]
    msg = "#{Time.now.utc.to_i}:#{SecureRandom.hex}"
    if BitpassStorage.create(:source_message => msg)
      msg
    else
      false
    end
  end

  def url_encode str
    URI.escape(str,"/")
  end

end
