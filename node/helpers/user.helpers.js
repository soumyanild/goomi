
exports.jsonData = async (data) => {
  
    var json = {};
    json["id"] = data.id;
    json["full_name"] = data.full_name;
    json["profile_image"] = data.profile_image;
    json["followers_count"] = data.my_followers;
    json["following_count"] = data.my_following;

  return json;
}