export interface MapboxFeature {
    id: string;
    place_name: string;
    geometry: {
        coordinates: [number, number]; // [longitude, latitude]
    };
    properties: Record<string, unknown>;
    text: string;
    place_type: string[];
    center: [number, number];
}

export interface MapboxResponse {
    features: MapboxFeature[];
    attribution: string;
    query: string[];
    type: string;
}
