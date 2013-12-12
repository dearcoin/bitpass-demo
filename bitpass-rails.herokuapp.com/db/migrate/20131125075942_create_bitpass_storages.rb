class CreateBitpassStorages < ActiveRecord::Migration
  def change
    create_table :bitpass_storages do |t|
      t.string :source_message
      t.string :btc_address
      t.string :signature_base64
      t.string :verify_time
      t.decimal :latitude
      t.decimal :longitude

      t.timestamps
    end
  end
end
